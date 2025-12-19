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

        // Handle Print Logging (safe)
        if ($request->has('print') && (int)$request->input('print') === 1) {
            $this->logAction('Print', $id, $mainReservation);
        }

        // Logs must never break the page
        $logs = collect();

        // Only attempt logs if table exists
        if (Schema::hasTable('system_logs')) {
            try {
                $logs = SystemLog::where('confirmation_number', $id)
                    ->orWhere('description', 'like', "%$id%")
                    ->orderBy('created_at', 'desc')
                    ->take(50)
                    ->get();
            } catch (\Throwable $e) {
                $logs = collect(); // absolute safe fallback
            }
        }

        return view('admin.reservations.show', compact(
            'reservations', 'mainReservation', 'user', 'payments', 'logs'
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
        // Hard bypass: if table not ready, do nothing
        if (!Schema::hasTable('system_logs')) {
            return;
        }

        try {
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
        } catch (\Throwable $e) {
            // bypass silently
            return;
        }
    }
}
