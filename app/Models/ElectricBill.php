<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricBill extends Model
{
    use HasFactory;

    protected $table = 'electric_bills';
    protected $guarded = [];
}
