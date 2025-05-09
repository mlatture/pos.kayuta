<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessSettings extends Model
{
    use HasFactory;

    protected $table = 'business_settings';
    public $timestamps = false;
    protected $fillable = [
        'type',
        'value'
    ];

    public static function set($key, $value)
    {
        if (is_null($value)) return;

        static::updateOrInsert(
            ['type' => $key],
            ['value' => $value, 'updated_at' => now()]
        );
    }
}
