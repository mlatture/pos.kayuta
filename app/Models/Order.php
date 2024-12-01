<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
use App\Models\PosPayment;
use App\Models\Reservation;

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

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }



    // Relationship with customer (user)
    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
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

    // Find order by ID with relationships
    public static function orderFindById($id)
    {
        return self::with(['reservations','posPayments',  'items', 'customer', 'admin'])->find($id);
    }

    // Get all orders with optional filters
    public function getAllOrders($where = [], $filters = [])
    {
        return self::with(['reservations', 'posPayments', 'items', 'items',  'customer', 'admin'])
            ->where($where)
            ->when(isset($filters['date_range']), function ($query) use ($filters) {
                $dates = explode(' - ', $filters['date_range']);
                $query->whereBetween(
                    $filters['date_to_use'] ?? 'created_at',
                    [date('Y-m-d', strtotime($dates[0])), date('Y-m-d', strtotime($dates[1]))]
                );
            })
            ->get();
    }
}
