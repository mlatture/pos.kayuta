<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin;
use App\Models\Product;

class DailyInventoryTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'product_id',
        'status',
        'assigned_at',
        'completed_at',
    ];

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    
}
