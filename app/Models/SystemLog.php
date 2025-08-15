<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'system_logs';
    protected $fillable = [
        'transaction_type',
        'sale_amount',
        'status',
        'payment_type',
        'confirmation_number',
        'customer_name',
        'customer_email',
        'user_id',
        'description',
        'before',
        'after',
        'created_at',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'created_at' => 'datetime',
    ];

    
}
