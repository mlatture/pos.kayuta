<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'payments';

    protected $fillable = [
        'cartid',
        'receipt',
        'method',
        'customernumber',
        'email',
        'payment',
    ];

    protected $casts = [
        'payment' => 'float',
    ];

    public function storePayment($data = [])
    {
        return self::create($data);
    }
}
