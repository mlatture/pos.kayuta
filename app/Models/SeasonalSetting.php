<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalSetting extends Model
{
    use HasFactory;

    protected $table = 'seasonal_settings';

    protected $fillable = [
        'default_rate', 'discount_percentage',
        'renewal_deadline', 'deposit_amount', 'rate_tiers'
    ];

    protected $casts = [
        'rate_tiers' => 'array',
        'renewal_deadline' => 'date',
    ];
}
