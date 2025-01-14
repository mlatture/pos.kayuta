<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardsOnFile extends Model
{
    use HasFactory;

    protected $table = 'cards_on_files';

    protected $fillable = [
        'name',
        'customernumber',
        'cartid',
        'receipt',
        'email',
        'xmaskedcardnumber',
        'method',
        'xtoken',
        'gateway_response',
        'createdate',
        'lastmodified'
    ];
    protected $guarded = [];

   
    public function storeCards($data = [])
    {
        return self::create($data);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'receipt', 'id');
    }

}

