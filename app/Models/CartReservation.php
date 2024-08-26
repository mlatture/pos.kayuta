<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartReservation extends Model
{
    use HasFactory;

    protected $table = 'cart_reservations';
    protected $date = ['cid', 'cod'];

    protected $guarded = [];
}
