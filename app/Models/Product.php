<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $guarded = [];

    protected $fillable = ['suggested_addon', 'category_id', 'tax_type_id', 'name', 'description', 'image', 'barcode', 'price', 'quantity', 'discount_type', 'discount', 'status', 'product_vendoer_id', 'cost', 'dni', 'last_checked_date', 'category', 'account', 'markup', 'profit', 'quick_pick', 'show_in_category'];

    public function taxType()
    {
        return $this->belongsTo(TaxType::class, 'tax_type_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
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
        return $this->belongsTo(ProductVendor::class, 'product_vendor_id', 'id');
    }
}
