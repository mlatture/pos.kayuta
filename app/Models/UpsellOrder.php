<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsellOrder extends Model
{
    use HasFactory;
    protected $table = 'upsell_orders';

    protected $fillable = [ 
        'order_number', 'cashier', 'upsell_text_id',
    ];
}
