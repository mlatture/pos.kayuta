<?php

namespace App\Models;

use Illuminate\Database\Eloquent\{Factories\HasFactory, Model};

class DictionaryTable extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'viewable' => 'boolean'
    ];
}