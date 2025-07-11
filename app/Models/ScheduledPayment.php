<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledPayment extends Model
{
    use HasFactory;

    protected $table = 'scheduled_payments';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'payment_date',
        'payment_type',
        'reference_key',
        'reservation_id',
        'reminder_sent',
        'recurring',
        'frequency',
        'status',
        'amount'

    ];

    public $casts = [
        'response_date' => 'array'
    ];
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_email', 'email');
    }
    
}
