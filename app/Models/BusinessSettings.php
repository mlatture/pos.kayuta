<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSettings extends Model
{
    use HasFactory;

    protected $table = 'business_Settings';
    protected $fillable = [
        'type',
        'value'
    ];
}
