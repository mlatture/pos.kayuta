<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledPayment extends Model
{
    use HasFactory;

    protected $table = 'scheduled_payments';

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
    
}
