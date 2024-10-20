<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationRegisters extends Model
{
    use HasFactory;

    protected $table = 'registers';

    protected $fillable = ['name'];
}


