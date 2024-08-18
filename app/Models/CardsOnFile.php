<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardsOnFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function storeCards($data = [])
    {
        return self::create($data);
    }
}
