<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\TaxType;
use App\Models\CardsOnFile;
use Carbon\Carbon;
use App\Models\User;
use App\Models\StationRegisters;

class ZoutModel extends Model
{
    use HasFactory;

    public function orders()
    {
        return $this->hasMany(Order::class, 'admin_id', 'admin_id');    
    }

    public static function getGrossSales($customerId, $date = null)
    {
        $query = Order::where('admin_id', $customerId);
    
        if (!empty($date)) {
            $specificDate = Carbon::parse($date)->startOfDay();
            $query->whereDate('created_at', $specificDate);
        }
    
        $grossSales = $query->sum('amount');
        $transactionCount = $query->count();
    
        return [
            'gross_sales' => $grossSales,
            'transaction_count' => $transactionCount,
        ];
    }
    

    public static function getTax($customerId, $date = null)
    {
        $customerOrderIds = Order::where('admin_id', $customerId);
    
        if (!empty($date)) {
            $specificDate = Carbon::parse($date)->startOfDay();
            $customerOrderIds->whereDate('created_at', $specificDate);
        }
    
        $totalTax = OrderItem::whereIn('order_id', $customerOrderIds->pluck('id'))->sum('tax');
    
        return $totalTax;
    }
    

    public static function getNetSales($customerId, $date = null)
    {
        $salesData = self::getGrossSales($customerId, $date);
        $totalTax = self::getTax($customerId, $date);
    
        $netSales = $salesData['gross_sales'] - $totalTax;
    
        return [
            'gross_sales' => $salesData['gross_sales'],
            'transaction_count' => $salesData['transaction_count'],
            'total_tax' => $totalTax,
            'net_sales' => $netSales,
        ];
    }

    public static function getSalesActivity($customerId, $date = null)
    {
        $customerOrderIds = Order::where('admin_id', $customerId);
    
        if (!empty($date)) {
            $specificDate = Carbon::parse($date)->startOfDay();
            $customerOrderIds->whereDate('created_at', $specificDate);
        }
    
        $orderItems = OrderItem::whereIn('order_id', $customerOrderIds->pluck('id'))
            ->with(['product.taxType'])
            ->get();
    
        $salesActivity = $orderItems->groupBy(function ($orderItem) {
            return $orderItem->product->taxType->title ?? 'Untaxed';
        })->map(function ($items, $taxType) {
            return [
                'tax_type' => $taxType,
                'gross_sales' => $items->sum('price'),
                'total_tax' => $items->sum('tax'),
            ];
        });
    
        return $salesActivity->values()->toArray();
    }

    public static function getPaymentSummary($customerId, $date = null)
    {
        $orderItems = OrderItem::with(['order', 'order.posPayments'])
            ->whereHas('order', function ($query) use ($customerId, $date) {
                $query->where('admin_id', $customerId);

                if (!empty($date)) {
                    $specificDate = Carbon::parse($date)->startOfDay();
                    $query->whereDate('created_at', $specificDate);
                }
            })
            ->get();

        $totals = [
            'cash' => ['total' => 0, 'count' => 0],
            'giftcard' => ['total' => 0, 'count' => 0],
            'check' => ['total' => 0, 'count' => 0],
            'creditcard' => ['total' => 0, 'count' => 0],
        ];

        foreach ($orderItems as $item) {
            foreach ($item->order->posPayments as $payment) {
                $method = strtolower($payment->payment_method);
                $amount = $payment->amount;

                if (array_key_exists($method, $totals)) {
                    $totals[$method]['total'] += $amount;
                    $totals[$method]['count']++;
                } else {
                    $totals['creditcard']['total'] += $amount;
                    $totals['creditcard']['count']++;
                }
            }
        }

        $formattedResult = [];
        foreach ($totals as $method => $data) {
            $formattedResult[] = [
                'payment_method' => ucfirst($method),
                'total_amount' => $data['total'],
                'transaction_count' => $data['count'],
            ];
        }

        return $formattedResult;
    }

    public static function getCreditCardListing($customerId, $date = null)
    {
        $cardDetails = CardsOnFile::with(['order'])
            ->whereHas('order', function ($query) use ($customerId, $date) {
                $query->where('admin_id', $customerId);

                // Apply date filter if provided
                if (!empty($date)) {
                    $specificDate = \Carbon\Carbon::parse($date)->format('Y-m-d');
                    $query->whereDate('created_at', $specificDate);
                }
            })
            ->get()
            ->map(function ($card) {
                return [
                    'method' => $card->method ?? 'Unknown',
                    'name' => $card->name ?? 'Unknown',
                    'masked_card_number' => $card->xmaskedcardnumber ?? '****',
                    'order_amount' => $card->order->amount ?? 0,
                ];
            });

        return $cardDetails;
    }

    
    public static function getUserActivity($customerId, $date)
    {
        $query = Order::query();
    
        if (!empty($date)) {
            $query->whereDate('created_at', Carbon::parse($date)->format('Y-m-d'));
        }
    
        if (!empty($customerId)) {
            $query->where('admin_id', $customerId);
        }
    
        $orders = $query->with(['posPayments'])->get();
    
        $hourlyCounts = array_fill(0, 24, 0); 
        $hourlyPaymentCounts = array_fill(0, 24, 0); 
    
        foreach ($orders as $order) {
            $orderHour = Carbon::parse($order->created_at)->hour;
            $hourlyCounts[$orderHour]++;
    
            foreach ($order->posPayments as $payment) {
                $paymentHour = Carbon::parse($payment->created_at)->hour;
                $hourlyPaymentCounts[$paymentHour]++;
            }
        }
    
        $totalPaymentCount = array_sum($hourlyPaymentCounts);
    
        return [
            'date' => $date,
            'total_count' => $orders->count(),
            'hourly_counts' => $hourlyCounts,
            'hourly_payment_counts' => $hourlyPaymentCounts,
            'total_payment_count' => $totalPaymentCount,
        ];
    }
    
    
}
