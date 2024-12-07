<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\PosPayment;
use App\Models\Reservation;
use App\Models\Admin;
use Exception;
class Order extends Model
{
    protected $guarded = [];
    protected $fillable = ['user_id', 'admin_id', 'total', 'amount', 'order_id', 'customer_id', 'gift_card_amount', 'received_amount', 'created_at', 'updated_at'];

    // Define relationship with order items
    public function items()
    {
        return $this->hasMany(OrderItem::class)->with(['product', 'product.taxType']);
    }

    // Alias for items if used elsewhere
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Define relationship with payments
    public function posPayments()
    {
        return $this->hasMany(PosPayment::class, 'order_id');
    }

    

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'customernumber');
    }


    public function getSourceAttribute()
    {
        if($this->posPayments->isNotEmpty()){
            return 'POS';
        }

        if($this->reservations->isNotEmpty()){
            return 'Reservation';
        }

        return '';
    }



    // Relationship with customer (user)
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    // Get customer name
    public function getCustomerName()
    {
        if ($this->customer) {
            return $this->customer->f_name . ' ' . $this->customer->l_name;
        }
        return 'Working Customer';
    }

    // Calculate the total using accessor
    public function getTotalAttribute()
    {
        $orderItemTotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $orderTotal = $orderItemTotal - ($this->gift_card_amount ?? 0); 

        return $orderTotal;
    }

    // Format the total
    public function formattedTotal()
    {
        return number_format($this->total, 2);
    }

    // Calculate the received amount
    public function receivedAmount()
    {
        return $this->posPayments->sum('amount'); 
    }

    // Format the received amount
    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }

  
    public static function orderFindById($id)
    {
        return self::with(['posPayments',  'items', 'customer'])->find($id);
    }

    public function getAllOrders($where = [], $filters = [])
    {
        return self::with(['reservations', 'posPayments', 'items', 'customer', 'admin'])
            ->when(isset($filters['date_range']), function ($query) use ($filters) {
                $dates = explode(' - ', $filters['date_range']);
    
                if (count($dates) === 2) {
                    $startDate = now()->parse(trim($dates[0]))->setTimezone('UTC')->startOfDay();
                    $endDate = now()->parse(trim($dates[1]))->setTimezone('UTC')->endOfDay();
                } else {
                    throw new Exception('Invalid date range format');
                }
    
                $dateToUse = $filters['date_to_use'] ?? 'transaction_date';
    
                switch ($dateToUse) {
                    case 'transaction_date':
                        $query->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereHas('reservations', function ($q) use ($startDate, $endDate) {
                                $q->whereBetween('created_at', [$startDate, $endDate]);
                            });
                        break;
    
                    case 'checkin_date':
                        $query->whereHas('reservations', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('checkedin', [$startDate, $endDate]);
                        });
                        break;
    
                    case 'arrival_date':
                        $query->whereHas('reservations', function ($q) use ($startDate, $endDate) {
                            $q->whereBetween('cid', [$startDate, $endDate]);
                        });
                        break;
    
                    case 'staying_on':
                        $query->whereHas('reservations', function ($q) use ($startDate, $endDate) {
                            $q->where('cid', '<=', $endDate)
                                ->where('cod', '>=', $startDate);
                        });
                        break;
    
                    default:
                        $query->whereBetween('created_at', [$startDate, $endDate])
                            ->orWhereHas('reservations', function ($q) use ($startDate, $endDate) {
                                $q->whereBetween('created_at', [$startDate, $endDate]);
                            });
                         
                        break;
                }
            })
            ->where($where)
            ->get();
    }
    
    
    
}
