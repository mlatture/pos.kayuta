<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    use HasFactory;
    protected $table = 'refunds';

    protected $fillable = [
        'cartid',
        'amount',
        'cancellation_fee',
        'reservations_id',
        'reason',
        'method',
        'x_ref_num',
        'override_reason',
        'created_by',
        'register_id',
    ];

}
