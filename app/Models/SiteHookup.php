<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteHookup extends Model
{
    protected $guarded = [];

    protected $table = 'site_hookups';
    protected $fillable = ['id', 'sitehookup', 'orderby'];
}
