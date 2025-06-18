<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    use HasFactory;

    protected $table = 'bills';

    protected $fillable = [
        'reservation_id',
        'customer_id',
        'kwh_used',
        'rate',
        'total_cost',
        'reading_dates',
        'auto_email',
    ];

    protected $casts = [
        'reading_dates' => 'array',
        'auto_email' => 'boolean',
    ];
    

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }


}
