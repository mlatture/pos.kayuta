<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = [
        'key', 'value', 'is_grid_view', 'golf_listing_show', 'boat_listing_show',
        'pool_listing_show', 'product_listing_show'
    ];

    public static function gridViewUpdate($key, $flag)
    {
        if (is_null($flag)) return;

        $enumValue = $flag ? 'yes' : 'no';

        static::updateOrInsert(
            ['key' => (string) $key],
            ['is_grid_view' => $flag, 'updated_at' => now()]
        );
        
    }


}
