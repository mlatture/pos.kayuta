<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalPayment extends Model
{
    use HasFactory;
    protected $fillable = [
        'cartid',
        'reservation_id',
        'amount',
        'tax',
        'total',
        'method',
        'x_ref_num',
        'receipt',
        'comment',
        'created_by',
    ];
}
