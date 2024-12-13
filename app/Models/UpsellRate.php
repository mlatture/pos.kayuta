<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsellRate extends Model
{
    use HasFactory;

    protected $table = 'upsell_rate';

    protected $fillable = [
        'rate_percent',
    
    ];

}
