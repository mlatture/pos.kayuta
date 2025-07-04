<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalAddOns extends Model
{
    use HasFactory;

    protected $table = 'seasonal_add_ons';

    protected $fillable = [
        'seasonal_add_on_name',
        'seasonal_add_on_price',
        'max_allowed',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
    

}
