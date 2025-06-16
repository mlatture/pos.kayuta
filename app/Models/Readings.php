<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Readings extends Model
{
    use HasFactory;

    protected $table = 'readings';

    protected $fillable = [
        'kwhNo',
        'image',
        'date',
        'siteno',
        'status',
        'bill',
        'customer_id',

    ];
}
