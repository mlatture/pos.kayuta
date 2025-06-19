<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalRenewal extends Model
{
    use HasFactory;
    protected $table = 'seasonal_renewals';

    protected $fillable = [
        'customer_id', 'offered_rate', 'renewed',
        'response_date', 'status', 'notes'
    ];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }

}
