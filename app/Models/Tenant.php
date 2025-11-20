<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = ['name', 'slug', 'domain', 'active'];

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}