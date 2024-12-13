<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
class TaxType extends Model
{
    use HasFactory;

    protected $table = 'tax_types';
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'tax_type_id');
    }
   
}
