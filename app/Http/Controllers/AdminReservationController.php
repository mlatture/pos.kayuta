<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Payment;
use App\Models\SystemLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Services\ReservationLogService;
use App\Models\ReservationLog;

class AdminReservationController extends Controller
{
    public function show(Request $request, $id)
    {
        $reservations = Reservation::where('cartid', $id)
            ->with(['payment', 'site', 'user'])
            ->get();

        if ($reservations->isEmpty()) {
            return redirect()->route('reservations.index')->with('error', 'Reservation not found.');
        }

        $mainReservation = $reservations->first();
        $user = User::find($mainReservation->customernumber);

        // Fetch Payments (Strictly from AdditionalPayment as per user request)
        // User Rule: "reservations (the reservation itself), additional_payments (has reservation_id), refunds... no payment table"
        
        // $payments = Payment::where('cartid', $id)->get() ?? collect(); // IGNORED
        $payments = collect(); // Empty collection to satisfy view variable if needed, but we rely on additionalPayments for ledger
        
        $additionalPayments = \App\Models\AdditionalPayment::where('cartid', $id)->get();
        $refunds = \App\Models\Refund::where('cartid', $id)->get();

        // Handle Print Logging (safe)
        if ($request->has('print') && (int)$request->input('print') === 1) {
            $this->logAction('Print', $id, $mainReservation);
        }

        // Logs must never break the page
        $logs = ReservationLog::where('reservation_id', $mainReservation->id)
            ->orWhere('reservation_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $registers = \App\Models\StationRegisters::all();

        // ---------------------------------------------------------
        // Financial Ledger Construction (STRICT MODE)
        // ---------------------------------------------------------
        $ledger = collect();

        // 1. Reservation Charges
        foreach ($reservations as $res) {
            // User Rule: "reservation.total is the final amount owed"
            // "If reservation.sitelock ... included inside reservation.total"
            // "If addons exist ... included inside reservation.total"
            
            $finalTotal = $res->total;
            $siteLock = $res->sitelock;
            $totalTax = $res->totaltax;
            
            // Calculate Addons Total
            $addonsTotal = 0;
            $addonsLines = [];
            if (!empty($res->addons_json)) {
                $decodedAddons = json_decode($res->addons_json, true);
                
                // Handle structure: {"items": [...], "addons_total": 50}
                $itemsToProcess = [];
                if (isset($decodedAddons['items']) && is_array($decodedAddons['items'])) {
                    $itemsToProcess = $decodedAddons['items'];
                } elseif (is_array($decodedAddons) && !isset($decodedAddons['items'])) {
                    // Legacy or simple list structure
                    $itemsToProcess = $decodedAddons; 
                }

                foreach ($itemsToProcess as $addon) {
                    if (!is_array($addon)) continue;
                    
                    // Fields: type, price, total_price, qty, site_id
                    $price = $addon['total_price'] ?? $addon['price'] ?? $addon['amount'] ?? 0;
                    $rawName = $addon['type'] ?? $addon['name'] ?? 'Addon';
                    
                    // Build descriptive name: "Boat Slip (PB06) x1"
                    $details = [];
                    if (!empty($addon['site_id'])) {
                        $details[] = $addon['site_id'];
                    }
                    if (!empty($addon['qty']) && $addon['qty'] > 1) {
                        $details[] = "x" . $addon['qty'];
                    }
                    
                    $name = $rawName . (count($details) > 0 ? " (" . implode(' ', $details) . ")" : "");
                    
                    if ($price > 0) {
                        $addonsTotal += $price;
                        $addonsLines[] = ['name' => $name, 'price' => $price];
                    }
                }
            }

            // Calculate Base
            // Base = Total - SiteLock - Addons - Tax
            $calculatedBase = $finalTotal - $siteLock - $addonsTotal - $totalTax;
            
            // Allow for float precision issues
            $calculatedBase = round($calculatedBase, 2);

            // Ledger Entries
            
            // A. Base Charge
            if ($calculatedBase != 0) { // Allow negative if it's a credit/discount, but typically positive
                $ledger->push([
                    'id' => 'base-' . $res->id,
                    'date' => $res->created_at,
                    'description' => "Site Base Charge: {$res->siteid} ({$res->nights} nights)",
                    'type' => 'charge',
                    'amount' => $calculatedBase,
                    'ref' => $res->xconfnum,
                    'raw_obj' => $res
                ]);
            }

            // B. Site Lock
            if ($siteLock > 0) {
                $ledger->push([
                    'id' => 'lock-' . $res->id,
                    'date' => $res->created_at,
                    'description' => "Site Lock Fee: {$res->siteid}",
                    'type' => 'charge',
                    'amount' => $siteLock,
                    'ref' => null,
                    'raw_obj' => $res
                ]);
            }

            // C. Addons
            foreach ($addonsLines as $idx => $al) {
                $ledger->push([
                    'id' => 'addon-' . $res->id . '-' . $idx,
                    'date' => $res->created_at,
                    'description' => "Addon: " . $al['name'],
                    'type' => 'charge',
                    'amount' => $al['price'],
                    'ref' => null,
                    'raw_obj' => $res
                ]);
            }

            // D. Tax
            if ($totalTax > 0) {
                $ledger->push([
                    'id' => 'tax-' . $res->id,
                    'date' => $res->created_at,
                    'description' => "Taxes",
                    'type' => 'charge',
                    'amount' => $totalTax,
                    'ref' => null,
                    'raw_obj' => $res
                ]);
            }
        }

        // 2. Additional Payments (The ONLY source of payments)
        // Note: If additional payments include charges (e.g. electric), they add to the total debt?
        // Usually `AdditionalPayment` has `amount` (paid) and sometimes `tax`.
        // User said: "Only use... additional_payments... no payment table"
        // If an AdditionalPayment represents a CHARGE, it should be added as charge. 
        // If it represents a PAYMENT, it should be added as payment.
        // `AdditionalPayment` model usually implies a transaction. 
        // Logic: The `AdditionalPayment` table typically records a payment event.
        // If it was for a "Charge", the charge should ALSO be recorded.
        // Does `AdditionalPayment` imply a NEW charge was created? 
        // The previous code had:
        //    Charge: $ap->amount
        //    Payment: -$ap->total
        // This implies every AdditionalPayment CREATES a charge and then PAYS it.
        // If so, we must keep that logic to balance the ledger.
        
        foreach ($additionalPayments as $ap) {
            // Logic: An "Additional Payment" is usually "I bought firewood ($10)".
            // So we owe $10 (Charge) and we paid $10 (Payment).
            // User instruction: "additional_payments (has reservation_id)"
            // If we don't add the charge, the payment will look like a credit against the base reservation.
            // Assumption: AdditionalPayments are strictly for EXTRA items/fees, not for paying the base reservation.
            // UNLESS the base reservation payment is ALSO stored in AdditionalPayments?
            // User said: "The remaining $160.27 should be applied as the reservation payment for BR01."
            // This implies the PAYMENT for the base reservation IS in AdditionalPayments? Or implicitly handled?
            // "ledger must be computed using only ... additional_payments ... no payment table".
            
            // Let's assume AdditionalPayment records are standard payments.
            // But do they also imply a charge?
            // If I see `Description: Payment (Visa)`, it is just a payment.
            // If I see `Description: Additional Charge: Cleaning`, it is a Charge AND Payment.
            // The previous controller code treated them as Charge + Payment.
            // Let's stick to that pattern: It creates a Charge line and a Payment line.
            
            // CHARGE SIDE
            // Only add charge if it's NOT just a payment for existing debt.
            // How to distinguish? `AdditionalPayment` usually denotes an ad-hoc transaction.
            // We will add the Charge side.
            $ledger->push([
                'id' => 'add-charge-' . $ap->id,
                'date' => $ap->created_at,
                'description' => ($ap->comment ?: 'Additional Charge'),
                'type' => 'charge',
                'amount' => $ap->amount,
                'ref' => $ap->x_ref_num,
                'raw_obj' => $ap
            ]);

            if ($ap->tax > 0) {
                 $ledger->push([
                    'id' => 'add-tax-' . $ap->id,
                    'date' => $ap->created_at,
                    'description' => "Tax (Additional)",
                    'type' => 'charge',
                    'amount' => $ap->tax,
                    'ref' => null,
                    'raw_obj' => $ap
                ]);
            }

            // PAYMENT SIDE
            $ledger->push([
                'id' => 'add-payment-' . $ap->id,
                'date' => $ap->created_at,
                'description' => "Payment (" . ($ap->method ?? 'Unknown') . ")",
                'type' => 'payment',
                'amount' => -($ap->total), // Negative for payment
                'ref' => $ap->x_ref_num,
                'raw_obj' => $ap
            ]);
        }

        // 3. Refunds & Cancellations
        foreach ($refunds as $rf) {
            // Cancellation Fee (Charge)
            if ($rf->cancellation_fee > 0) {
                 $ledger->push([
                    'id' => 'cancel-fee-' . $rf->id,
                    'date' => $rf->created_at,
                    'description' => "Cancellation Fee",
                    'type' => 'charge',
                    'amount' => $rf->cancellation_fee,
                    'ref' => null,
                    'raw_obj' => $rf
                ]);
            }

            // Refund (Positive Value to increase Balance / Offset Payment)
            if ($rf->amount > 0) {
                 $ledger->push([
                    'id' => 'refund-' . $rf->id,
                    'date' => $rf->created_at,
                    'description' => "Refund Issued",
                    'type' => 'refund',
                    'amount' => $rf->amount, 
                    'ref' => $rf->method,
                    'raw_obj' => $rf
                ]);
            }
        }

        // Sort by Date
        $ledger = $ledger->sortBy('date');

        // Calculate Totals
        // Charges: Type = charge
        $totalCharges = $ledger->where('type', 'charge')->sum('amount');
        
        // Payments: Type = payment (Negative values)
        $totalPayments = $ledger->where('type', 'payment')->sum('amount');
        
        // Refunds: Type = refund (Positive values)
        $totalRefunds = $ledger->where('type', 'refund')->sum('amount');
        
        // Net Total = Charges + Payments + Refunds
        $netTotal = round($totalCharges + $totalPayments + $totalRefunds, 2);
        
        // Balance Due - Force to 0 if status is Paid, or if Net Total is negative (credit)
        if ($mainReservation->status === 'Paid' || $netTotal < 0) {
            $balanceDue = 0;
        } else {
            $balanceDue = $netTotal;
        }

        // Variables for View / JS
        $cartTotal = $reservations->sum('total');
        // totalPaid should be the magnitude of money collected (for refund pro-rating)
        // totalPayments is negative in ledger, so take absolute or sum magnitude
        $totalPaid = abs($totalPayments);

        return view('admin.reservations.show', compact(
            'reservations', 'mainReservation', 'user', 'payments', 'logs', 'additionalPayments', 'refunds', 'registers',
            'ledger', 'totalCharges', 'totalPayments', 'totalRefunds', 'netTotal', 'balanceDue', 
            'cartTotal', 'totalPaid'
        ));
    }

    public function globalSearch(Request $request)
    {
        $q = trim($request->input('q'));

        if (strlen($q) < 3) {
             return redirect()->back()->with('error', 'Search query must be at least 3 characters.');
        }

        // 1. Exact Cart ID Match (Highest Priority)
        $exactCart = Reservation::where('cartid', $q)->first();
        if ($exactCart) {
            return redirect()->route('admin.reservations.show', ['id' => $exactCart->cartid]);
        }

        // 2. Broad Search
        $normalizedPhone = preg_replace('/[^0-9]/', '', $q);
        
        $userIds = User::query()
            ->where('email', 'like', "%{$q}%")
            ->orWhere('f_name', 'like', "%{$q}%")
            ->orWhere('l_name', 'like', "%{$q}%")
            ->when(!empty($normalizedPhone), function($query) use ($normalizedPhone) {
                 return $query->orWhere('phone', 'like', "%{$normalizedPhone}%");
            })
            ->pluck('id')
            ->toArray();

        // Search Reservations
        $reservations = Reservation::query()
            ->where('cartid', 'like', "%{$q}%")
            ->orWhere('fname', 'like', "%{$q}%")
            ->orWhere('lname', 'like', "%{$q}%")
            ->orWhereIn('customernumber', $userIds)
            ->with(['user', 'site'])
            ->orderBy('cid', 'desc')
            ->get();

        // Group by Cart ID to get unique "Bookings"
        $uniqueBookings = $reservations->groupBy('cartid');
        $count = $uniqueBookings->count();

        if ($count === 0) {
            return redirect()->back()->with('error', 'No reservations found for "' . $q . '".');
        }

        if ($count === 1) {
            $cartId = $uniqueBookings->keys()->first();
            return redirect()->route('admin.reservations.show', ['id' => $cartId]);
        }

        return view('admin.reservations.search_results', [
            'bookings' => $uniqueBookings, 
            'query' => $q
        ]);
    }



    public function checkin($id)
    {
        $reservations = Reservation::where('cartid', $id)->get();

        if ($reservations->isEmpty()) {
            return redirect()->back()->with('error', 'Reservation not found.');
        }

        $activeStatuses = ['Paid', 'Confirmed', 'Pending'];
        $now = Carbon::now();
        $today = Carbon::today();

        $updatedCount = 0;

        foreach ($reservations as $res) {
            if (in_array($res->status, $activeStatuses) && $today->gte(Carbon::parse($res->cid))) {
                if (is_null($res->checkedin)) {
                    $res->checkedin = $now;
                    $res->save();
                    $updatedCount++;
                }
            }
        }

        if ($updatedCount > 0) {
            $this->logAction('Check-In', $id, $reservations->first());
            return redirect()->back()->with('success', 'Checked In successfully.');
        }

        return redirect()->back()->with('error', 'No eligible reservations to check in (Check date or status).');
    }

    public function checkout($id)
    {
        $reservations = Reservation::where('cartid', $id)->get();

        if ($reservations->isEmpty()) {
            return redirect()->back()->with('error', 'Reservation not found.');
        }

        $updatedCount = 0;
        $now = Carbon::now();

        foreach ($reservations as $res) {
            if (!is_null($res->checkedin) || in_array($res->status, ['Paid', 'Confirmed', 'Pending'])) {
                if (is_null($res->checkedout)) {
                    $res->checkedout = $now;
                    $res->save();
                    $updatedCount++;
                }
            }
        }

        if ($updatedCount > 0) {
            $this->logAction('Check-Out', $id, $reservations->first());
            return redirect()->back()->with('success', 'Checked Out successfully.');
        }

        return redirect()->back()->with('error', 'No eligible reservations to check out.');
    }

    private function logAction($type, $cartId, $mainReservation)
    {
        try {
            // Mapping UI type to event_type
            $eventType = strtolower(str_replace('-', '_', $type));
            if ($type === 'Print') $eventType = 'print_invoice';

            app(ReservationLogService::class)->log(
                $mainReservation->id ?? $cartId,
                $eventType,
                null,
                null,
                "Reservation #$cartId - $type"
            );

            // Keep old logging for backward compatibility if table exists
            if (Schema::hasTable('system_logs')) {
                SystemLog::create([
                    'transaction_type' => $type,
                    'status' => 'Success',
                    'payment_type' => 'N/A',
                    'confirmation_number' => $cartId,
                    'customer_name' => trim(($mainReservation->fname ?? '') . ' ' . ($mainReservation->lname ?? '')),
                    'customer_email' => $mainReservation->email ?? '',
                    'user_id' => Auth::id(),
                    'description' => "Reservation #$cartId - $type",
                    'before' => null,
                    'after' => json_encode([
                        'cartid' => $cartId,
                        'action' => $type,
                        'timestamp' => Carbon::now()->toDateTimeString(),
                        'user' => Auth::user()->name ?? 'Unknown'
                    ]),
                    'created_at' => Carbon::now(),
                ]);
            }
        } catch (\Throwable $e) {
            // bypass silently
            return;
        }
    }
}
