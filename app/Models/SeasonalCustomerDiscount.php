<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeasonalCustomerDiscount extends Model
{
    use HasFactory;

    protected $table = 'seasonal_customer_discounts';

    protected $fillable = ['customer_id', 'discount_type', 'discount_value', 'description', 'is_active', 'season_year', 'created_by'];

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
    public function create()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
