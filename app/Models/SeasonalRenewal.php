<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalRenewal extends Model
{
    use HasFactory;

    protected $table = 'seasonal_renewals';

    protected $fillable = ['customer_id', 'customer_name', 'customer_email', 'allow_renew', 'status', 'initial_rate', 'discount_percent', 'discount_amount', 'discount_note', 'final_rate', 'payment_plan', 'payment_plan_id', 'selected_payment_method', 'day_of_month', 'offered_rate', 'renewed', 'response_date', 'notes'];

    protected $casts = [
        'allow_renew' => 'boolean',
        'renewed' => 'boolean',
        'initial_rate' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_rate' => 'decimal:2',
        'offered_rate' => 'decimal:2',
        'response_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }


    // public function paymentPlan()
    // {
    //     return $this->belongsTo(PaymentPlan::class, 'payment_plan_id');
    // }

    public function getMaskedAccountAttribute()
    {
        if (!$this->selected_payment_method) {
            return '—';
        }

        return '**** ' . substr($this->selected_payment_method, -4);
    }
}
