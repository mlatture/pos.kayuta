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

        // Fetch Payments (safe)
        $payments = Payment::where('cartid', $id)->get() ?? collect();
        
        $additionalPayments = \App\Models\AdditionalPayment::where('cartid', $id)->get();
        $refunds = \App\Models\Refund::where('cartid', $id)->get();

        // Handle Print Logging (safe)
        if ($request->has('print') && (int)$request->input('print') === 1) {
            $this->logAction('Print', $id, $mainReservation);
        }

        // Logs must never break the page
        $logs = ReservationLog::where('reservation_id', $mainReservation->id)
            ->orWhere('reservation_id', $id) // in case it was logged by cart ID
            ->orderBy('created_at', 'desc')
            ->take(50)
            ->get();

        $registers = \App\Models\StationRegisters::all();

        // ---------------------------------------------------------
        // Financial Ledger Construction
        // ---------------------------------------------------------
        $ledger = collect();

        // Constants
        $additionalChargesTotal = $additionalPayments->sum('amount');
        $additionalTaxTotal = $additionalPayments->sum('tax');

        // 1. Reservation Charges
        foreach ($reservations as $res) {
            // "Base" Calculation Strategy:
            // The `total` column includes Base + SiteLock + Taxes + AdditionalPayments(Amount+Tax).
            // To isolate "Base", we must subtract everything else.
            
            // Note: If multiple reservations exist (split cart), AdditionalPayments are typically linked to ONE reservation_id.
            // We need to filter additional payments for THIS reservation to subtract correctly, OR
            // if additional payments are just cart-wide, we distribute?
            // `AdditionalPayment` has `reservation_id`.
            
            $resAdds = $additionalPayments->where('reservation_id', $res->id);
            $resAddAmount = $resAdds->sum('amount');
            $resAddTax = $resAdds->sum('tax');

            // Calculate Base if missing
            // We assume 'total' in reservations table is the ORIGINAL reservation total (Base + SiteLock + Tax).
            // It does NOT include external AdditionalPayment records.
            $calculatedBase = $res->total - $res->totaltax - $res->sitelock;
            
            // Use DB base if valid positive, else calculated
            // If DB base is 0, it might be legacy or error, so use calculated.
            $baseCharge = $res->base > 0 ? $res->base : $calculatedBase;
            
            // Adjust Tax: We want to show "Reservation Tax" separate from "Additional Tax" if we list Additional as a line item.
            // OR we list "Total Tax" and exclude tax from Additional line.
            // Let's list Additional Payment as "Item ($Amount)" and separate "Item Tax ($Tax)".
            // Actually, usually Receipt shows "Item ... $Total".
            // Let's strip the Additional Tax from the main "Tax" line to avoid confusion.
            $displayTax = $res->totaltax - $resAddTax;

            // Clamp negative base (e.g. if discounts exceed base? We treat discount separately if possible, or just show net base)
            // For now, allow negative ONLY if it's a refund? No, Ledger Charges should be positive.
            // If logic yields negative, it implies data error or hidden discount.
            $baseCharge = max(0, $baseCharge);

            if ($baseCharge > 0) {
                $ledger->push([
                    'id' => 'base-' . $res->id,
                    'date' => $res->created_at,
                    'description' => "Site Base Charge: {$res->siteid} ({$res->nights} nights)",
                    'type' => 'charge',
                    'amount' => $baseCharge,
                    'ref' => $res->xconfnum,
                    'raw_obj' => $res
                ]);
            }

            if ($res->sitelock > 0) {
                $ledger->push([
                    'id' => 'lock-' . $res->id,
                    'date' => $res->created_at,
                    'description' => "Site Lock Fee: {$res->siteid}",
                    'type' => 'charge',
                    'amount' => $res->sitelock,
                    'ref' => null,
                    'raw_obj' => $res
                ]);
            }

            if ($displayTax > 0) {
                $ledger->push([
                    'id' => 'tax-' . $res->id,
                    'date' => $res->created_at,
                    'description' => "Taxes (Reservation)",
                    'type' => 'charge',
                    'amount' => $displayTax,
                    'ref' => null,
                    'raw_obj' => $res
                ]);
            }
        }

        // 2. Additional Charges & Payments
        foreach ($additionalPayments as $ap) {
            // Charge Side
            // Show Amount and Tax separately or together? 
            // Let's show "Charge: Item ($Amount)" and "Tax: Item ($Tax)"? 
            // Or "Charge: Item ($Total)".
            // Ledger typically separates Tax.
            // Let's add Charge Amount.
            $ledger->push([
                'id' => 'add-charge-' . $ap->id,
                'date' => $ap->created_at,
                'description' => "Additional Charge: " . ($ap->comment ?: 'Miscellaneous'),
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

            // Payment Side (It was paid immediately)
            // User request: "Additional payments as negative credits"
            $ledger->push([
                'id' => 'add-payment-' . $ap->id,
                'date' => $ap->created_at,
                'description' => "Payment (Additional Charge)",
                'type' => 'payment',
                'amount' => -($ap->total), // Full amount paid
                'ref' => $ap->x_ref_num,
                'raw_obj' => $ap
            ]);
        }

        // 3. Regular Payments
        foreach ($payments as $p) {
            $ledger->push([
                'id' => 'payment-' . $p->id,
                'date' => $p->created_at,
                'description' => "Payment (" . ucwords(str_replace('_', ' ', $p->method)) . ")",
                'type' => 'payment',
                'amount' => -($p->payment),
                'ref' => $p->x_ref_num,
                'raw_obj' => $p
            ]);
        }

        // 4. Refunds & Cancellations
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
            // If I paid -100 (Credit), and Get +20 (Debit/Refund), Net Credit is -80.
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
        // Example: 100 + (-100) + 20 = 20.
        $netTotal = round($totalCharges + $totalPayments + $totalRefunds, 2);
        
        // Force Balance Due to 0 if status is explicitly 'Paid' (User Request)
        if ($mainReservation->status === 'Paid') {
            $balanceDue = 0;
        } else {
            $balanceDue = max(0, $netTotal);
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
