<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CustomerBalanceService
{
    /**
     * Calculate the total balance for a given customer.
     *
     * @param User $customer
     * @return float
     */

    public function getDetailedBalance(User $customer)
    {
        $resDue = $customer
            ->reservations()
            ->whereIn('status', ['paid', 'cancelled'])
            ->whereHas('sites', function($query) {
                $query->where('seasonal', 0);
            })
            ->get()
            ->sum(function ($reservation) {
                return $reservation->totalcharges - $reservation->payments()->sum('payment');
            });
        
        $seasonalDue = $customer->reservations()
            ->whereIn('status', ['paid', 'cancelled'])
            ->whereHas('sites', function($query) {
                $query->where('seasonal', 1);
            })
            ->get()
            ->sum(function ($reservation) {
                return $reservation->totalcharges - $reservation->payments()->sum('payment');
            });

        $utilDue = $customer->utilityBills()->whereDoesntHave('paymentBill')->sum('total_cost');

        $posDue = Order::where('customer_id', $customer->id)
            ->get()
            ->sum(function ($order) {
                $paid = $order->posPayments->sum('amount');
                return max(0, $order->amount - $paid);
            });
        $giftCredit = $customer->giftCards()
            ->where('status', true)
            ->sum('amount');
            
        $total = $resDue + $utilDue + $seasonalDue + $posDue - $giftCredit;

        return [
            'total' => (float) $total,
            'parts' => [
                'r' => ['due' => (float) $resDue],
                'u' => ['due' => (float) $utilDue],
                's' => ['due' => (float) $seasonalDue],
                'p' => ['due' => (float) $posDue],
                'g' => ['credit' => (float) $giftCredit],
            ],
        ];
    }
}
