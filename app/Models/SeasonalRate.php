<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalRate extends Model
{
    use HasFactory;

    protected $table = 'seasonal_rates';

    protected $fillable = ['rate_name', 'rate_price', 'deposit_amount', 'early_pay_discount', 'full_payment_discount', 'payment_plan_starts', 'final_payment_due', 'template_id', 'applies_to_all', 'active'];

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }
}
