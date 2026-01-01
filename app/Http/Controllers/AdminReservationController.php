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
    // ---------------------------------------------------------
    // 1) Find clicked reservation
    // ---------------------------------------------------------
    $clickedReservation = Reservation::where('cartid', $id)->first();

    if (!$clickedReservation && is_numeric($id)) {
        $clickedReservation = Reservation::find((int)$id);
    }

    if (!$clickedReservation) {
        return redirect()->route('reservations.index')->with('error', 'Reservation not found.');
    }

    $customerId = $clickedReservation->customernumber ?? null;

    // Normalize cid/cod to DATE only (ignore time)
    $cidDate = null;
    $codDate = null;

    try {
        if (!empty($clickedReservation->cid)) {
            $cidDate = \Carbon\Carbon::parse($clickedReservation->cid)->format('Y-m-d');
        }
        if (!empty($clickedReservation->cod)) {
            $codDate = \Carbon\Carbon::parse($clickedReservation->cod)->format('Y-m-d');
        }
    } catch (\Throwable $e) {
        $cidDate = null;
        $codDate = null;
    }

    // ---------------------------------------------------------
    // 2) Group reservations (fixes missing SH08)
    // Primary: same customer + same cid/cod (date-only)
    // Secondary: payment_id / group code / xconfnum
    // ---------------------------------------------------------
    $paymentId = $clickedReservation->payment_id ?? null;
    $groupCode = $clickedReservation->group_confirmation_code ?? null;
    $xconfnum  = $clickedReservation->xconfnum ?? null;

    $reservations = Reservation::query()
        ->where(function ($q) use ($customerId, $cidDate, $codDate, $paymentId, $groupCode, $xconfnum) {

            // A) Same customer + same stay dates (core use case)
            if (!empty($customerId) && !empty($cidDate) && !empty($codDate)) {
                $q->orWhere(function ($q2) use ($customerId, $cidDate, $codDate) {
                    $q2->where('customernumber', $customerId)
                       ->whereDate('cid', $cidDate)
                       ->whereDate('cod', $codDate);
                });
            }

            // B) Same checkout payment (if linked)
            if (!empty($paymentId)) {
                $q->orWhere('payment_id', $paymentId);
            }

            // C) Group confirmation code (if you added it)
            if (!empty($groupCode)) {
                $q->orWhere('group_confirmation_code', $groupCode);
            }

            // D) Fallback: auth code
            if (!empty($xconfnum)) {
                $q->orWhere('xconfnum', $xconfnum);
            }
        })
        ->with(['payment', 'site', 'user'])
        ->orderBy('created_at', 'asc')
        ->get();

    if ($reservations->isEmpty()) {
        return redirect()->route('reservations.index')->with('error', 'Reservation not found.');
    }

    $mainReservation = $reservations->first();
    $user = User::find($mainReservation->customernumber);

    // ---------------------------------------------------------
    // 3) Pull ledger sources for the whole group
    // ---------------------------------------------------------
    $cartIds = $reservations->pluck('cartid')->filter()->unique()->values();

    $payments = collect(); // view compatibility
    $additionalPayments = \App\Models\AdditionalPayment::whereIn('cartid', $cartIds)->get();
    $refunds            = \App\Models\Refund::whereIn('cartid', $cartIds)->get();

    // Checkout payment: prefer payment_id on ANY reservation in group
    $checkoutPayment = null;
    $groupPaymentId = $reservations->pluck('payment_id')->filter()->unique()->first();

    if (!empty($groupPaymentId)) {
        $checkoutPayment = \App\Models\Payment::find($groupPaymentId);
    } else {
        $checkoutPayment = $mainReservation->payment ?? null;
    }

    // Print logging
    if ($request->has('print') && (int)$request->input('print') === 1) {
        $this->logAction('Print', $id, $mainReservation);
    }

    // Logs
    $logs = ReservationLog::where('reservation_id', $mainReservation->id)
        ->orWhere('reservation_id', $id)
        ->orderBy('created_at', 'desc')
        ->take(50)
        ->get();

    $registers = \App\Models\StationRegisters::all();

    // ---------------------------------------------------------
    // 4) Build ledger rows
    // ---------------------------------------------------------
    $ledger = collect();

    // If TRUE: reservation.total includes tax already
    $total_includes_tax = true;

    // A) Reservation charges
    foreach ($reservations as $res) {
        $finalTotal = (float) $res->total;
        $siteLock   = (float) ($res->sitelock ?? 0);
        $totalTax   = (float) ($res->totaltax ?? 0);

        // Site Charge excludes sitelock
        $siteCharge = round($finalTotal - ($siteLock > 0 ? $siteLock : 0), 2);

        $stayLabel = '';
        try {
            if (!empty($res->cid) && !empty($res->cod)) {
                $stayLabel = ' (' .
                    \Carbon\Carbon::parse($res->cid)->format('M d') .
                    '–' .
                    \Carbon\Carbon::parse($res->cod)->format('M d') .
                    ')';
            }
        } catch (\Throwable $e) {}

        if ($siteCharge != 0.0) {
            $ledger->push([
                'id'          => 'site-charge-' . $res->id,
                'date'        => $res->created_at,
                'description' => "Site Charge: {$res->siteid}{$stayLabel}",
                'type'        => 'charge',
                'amount'      => $siteCharge, // +
                'ref'         => $res->group_confirmation_code ?? ($res->xconfnum ?: ($res->confirmation_code ?? null)),
                'raw_obj'     => $res,
                'seq'         => 10,
            ]);
        }

        if ($siteLock > 0) {
            $ledger->push([
                'id'          => 'site-lock-' . $res->id,
                'date'        => $res->created_at,
                'description' => "Site Lock Fee",
                'type'        => 'charge',
                'amount'      => round($siteLock, 2), // +
                'ref'         => null,
                'raw_obj'     => $res,
                'seq'         => 20,
            ]);
        }

        if (!$total_includes_tax && $totalTax > 0) {
            $ledger->push([
                'id'          => 'tax-' . $res->id,
                'date'        => $res->created_at,
                'description' => "Taxes",
                'type'        => 'charge',
                'amount'      => round($totalTax, 2),
                'ref'         => null,
                'raw_obj'     => $res,
                'seq'         => 30,
            ]);
        }
    }

    // B) One checkout payment line
    if ($checkoutPayment) {
        $masked = null;
        try {
            $card = \App\Models\CardsOnFile::where('customernumber', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();
            $masked = $card->xmaskedcardnumber ?? null;
        } catch (\Throwable $e) {}

        $method = $checkoutPayment->method ?? 'Card';
        $desc   = "Payment – {$method}";
        if (!empty($masked)) {
            $desc .= " {$masked}";
        }

        $ledger->push([
            'id'          => 'checkout-payment-' . $checkoutPayment->id,
            'date'        => $checkoutPayment->created_at ?? $mainReservation->created_at,
            'description' => $desc,
            'type'        => 'payment',
            'amount'      => -abs((float) ($checkoutPayment->payment ?? 0)), // -
            'ref'         => $checkoutPayment->x_ref_num ?? null,
            'raw_obj'     => $checkoutPayment,
            'seq'         => 90,
        ]);
    }

    // C) Additional charges/payments
    foreach ($additionalPayments as $ap) {
        $apAmount = (float) ($ap->amount ?? 0);
        $apTax    = (float) ($ap->tax ?? 0);
        $apTotal  = (float) ($ap->total ?? 0);

        $looksLikePayment = !empty($ap->method) || !empty($ap->x_ref_num);

        if ($apAmount > 0) {
            $ledger->push([
                'id'          => 'add-charge-' . $ap->id,
                'date'        => $ap->created_at,
                'description' => ($ap->comment ?: 'Additional Charge'),
                'type'        => 'charge',
                'amount'      => $apAmount,
                'ref'         => $ap->x_ref_num,
                'raw_obj'     => $ap,
                'seq'         => 40,
            ]);
        }

        if ($apTax > 0) {
            $ledger->push([
                'id'          => 'add-tax-' . $ap->id,
                'date'        => $ap->created_at,
                'description' => "Tax (Additional)",
                'type'        => 'charge',
                'amount'      => $apTax,
                'ref'         => null,
                'raw_obj'     => $ap,
                'seq'         => 50,
            ]);
        }

        if ($looksLikePayment && $apTotal > 0) {
            $method = $ap->method ?? 'Card';
            $ledger->push([
                'id'          => 'add-payment-' . $ap->id,
                'date'        => $ap->created_at,
                'description' => "Payment – {$method}",
                'type'        => 'payment',
                'amount'      => -abs($apTotal),
                'ref'         => $ap->x_ref_num,
                'raw_obj'     => $ap,
                'seq'         => 60,
            ]);
        }
    }

    // D) Refunds
    foreach ($refunds as $rf) {
        if (($rf->cancellation_fee ?? 0) > 0) {
            $ledger->push([
                'id'          => 'cancel-fee-' . $rf->id,
                'date'        => $rf->created_at,
                'description' => "Cancellation Fee",
                'type'        => 'charge',
                'amount'      => (float) $rf->cancellation_fee,
                'ref'         => null,
                'raw_obj'     => $rf,
                'seq'         => 70,
            ]);
        }

        if (($rf->amount ?? 0) > 0) {
            $ledger->push([
                'id'          => 'refund-' . $rf->id,
                'date'        => $rf->created_at,
                'description' => "Refund Issued",
                'type'        => 'refund',
                'amount'      => (float) $rf->amount,
                'ref'         => $rf->method ?? null,
                'raw_obj'     => $rf,
                'seq'         => 80,
            ]);
        }
    }

    // Stable sort (same timestamps)
    $ledger = $ledger->sortBy([
        fn ($a, $b) => $a['date'] <=> $b['date'],
        fn ($a, $b) => ($a['seq'] ?? 0) <=> ($b['seq'] ?? 0),
        fn ($a, $b) => strcmp($a['id'], $b['id']),
    ])->values();

    // ---------------------------------------------------------
    // 5) Running balance EXACTLY like screenshot
    // ---------------------------------------------------------
    $running = 0.0;
    $ledger = $ledger->map(function ($row) use (&$running) {
        $running = round($running + (float) $row['amount'], 2);

        // Do not show negative balance; stop at 0.00
        $row['balance'] = max(0, $running);

        return $row;
    });

    // ---------------------------------------------------------
    // 6) Summary (separate from ledger rows)
    // ---------------------------------------------------------
    $totalCharges = round($ledger->where('type', 'charge')->sum('amount'), 2);

    // Payments are negative in ledger; display positive in summary
    $totalPaymentsSigned  = round($ledger->where('type', 'payment')->sum('amount'), 2);
    $totalPaymentsDisplay = round(abs($totalPaymentsSigned), 2);

    $outstandingBalance = (float) ($ledger->last()['balance'] ?? 0);
    $outstandingBalance = max(0, round($outstandingBalance, 2));

    // Compatibility
    $totalRefunds = round($ledger->where('type', 'refund')->sum('amount'), 2);
    $netTotal     = round($totalCharges + $totalPaymentsSigned + $totalRefunds, 2);
    $balanceDue   = $outstandingBalance;

    $cartTotal = $reservations->sum('total');
    $totalPaid = $totalPaymentsDisplay;

    return view('admin.reservations.show', compact(
        'reservations',
        'mainReservation',
        'user',
        'payments',
        'logs',
        'additionalPayments',
        'refunds',
        'registers',
        'ledger',
        'totalCharges',
        'totalPaymentsSigned',
        'totalPaymentsDisplay',
        'totalRefunds',
        'netTotal',
        'balanceDue',
        'outstandingBalance',
        'cartTotal',
        'totalPaid'
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
