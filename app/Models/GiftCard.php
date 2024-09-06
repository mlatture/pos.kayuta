<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'gift_cards';

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function storeGiftCard($data = [])
    {
        if(!isset($data['min_purchase']) or !$data['min_purchase']) {
            $data['min_purchase'] = 0;
        }
        if(!isset($data['max_discount']) or !$data['max_discount']) {
            $data['max_discount'] = 0;
        }
        return self::create($data);
    }

    public function giftCardFind($where = [], $filters = [])
    {
        return self::where($where)
            ->when(count($filters) > 0, function ($query) use ($filters) {
                $query->when(!empty($filters['date']), function ($query) use ($filters) {
                    $query->where('start_date', '<', $filters['date'])
                        ->where('expire_date', '>', $filters['date']);
                });
            })->first();
    }

    public function getAllGiftCardWithOrders($where = [], $filters = [])
    {
        return self::has('orders')->where($where)
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
