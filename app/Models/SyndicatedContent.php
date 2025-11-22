<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyndicatedContent extends Model
{
    protected $fillable = [
        'tenant_id',
        'idea_id',
        'channel',
        'title',
        'body_md',
        'meta',
        'status',
    ];

    protected $casts = [
        'meta' => 'array',
    ];
}
