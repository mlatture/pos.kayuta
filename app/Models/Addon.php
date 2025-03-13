<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;

    protected $table = 'addons';

    protected $fillable = [
        'addon_name',
        'price',
        'addon_type',
        'capacity'
    ];
}
