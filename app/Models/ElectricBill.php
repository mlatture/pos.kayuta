<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricBill extends Model
{
    use HasFactory;

    protected $table = 'electric_bills';
    protected $fillable = [
        'reading_id',
        'meter_number',
        'customer_id',
        'start_date',
        'end_date',
        'usage_kwh',
        'rate',
        'total',
        'threshold_used',
        'warning_overridden',
        'sent_at',
    ];
    
    protected $guarded = [];
}
