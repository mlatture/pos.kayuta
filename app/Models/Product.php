<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = [];

    protected $fillable = ['category_id', 'tax_type_id', 'name', 'description', 'image', 'barcode', 'price', 'quantity', 'status', 'type', 'discount_type', 'discount', 'product_vendor_id', 'cost'];

    public function taxType()
    {
        return $this->belongsTo(TaxType::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }


    public function getImageUrlAttribute()
    {
        return $this->image ? asset(Storage::url($this->image)) : asset('images/product-thumbnail.jpg');
    }

    public function productVendor(): BelongsTo
    {
        return $this->belongsTo(ProductVendor::class,'product_vendor_id','id');
    }
}
