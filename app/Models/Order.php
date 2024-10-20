<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;
class Order extends Model
{
    protected $guarded = [];
    protected $fillable = ['user_id', 'total', 'gift_card_amount', 'received_amount', 'created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany(OrderItem::class)->with(['product']);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PosPayment::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCustomerName()
    {
        if ($this->customer) {
            return $this->customer->f_name . ' ' . $this->customer->l_name;
        }
        return 'Working Customer';
    }

    public function total()
    {
        $orderItemTotal = $this->items->map(function ($i) {
            return $i->price;
        })->sum();

        $orderTotal = $orderItemTotal   -   $this->gift_card_amount;

        return $orderTotal;
    }

    public function formattedTotal()
    {
        return number_format($this->total(), 2);
    }

    public function receivedAmount()
    {
        return $this->payments->map(function ($i) {
            return $i->amount;
        })->sum();
    }

    public function formattedReceivedAmount()
    {
        return number_format($this->receivedAmount(), 2);
    }

    public static function orderFindById($id)
    {
        return self::with(['payments', 'items', 'customer'])->find($id);
    }

    public function getAllOrders($where = [], $filters = [])
    {
        return self::with(['payments', 'items', 'customer'])->where($where)
        ->when(count($filters) > 0, function ($query) use ($filters) {
            $query->when(isset($filters['date']) && !empty($filters['date']), function ($query) use ($filters) {
                $filters['date'] = explode('-', $filters['date']);
                $query->whereBetween(
                    'created_at',
                    [
                        date('Y-m-d', strtotime($filters['date'][0])),
                        date('Y-m-d', strtotime($filters['date'][1]))
                    ]
                );
            });
        })->get();
    }
}
