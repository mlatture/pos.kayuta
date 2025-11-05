<?php

namespace App\Models\ContentHub;

use Illuminate\Database\Eloquent\Model;

class AiToneProfile extends Model
{
    protected $fillable = [
        'channel','tone_settings','learned_examples','hashtag_preferences','content_guidelines'
    ];

    protected $casts = [
        'tone_settings'       => 'array', // <-- IMPORTANT
        'learned_examples'    => 'array',
        'hashtag_preferences' => 'array',
    ];
}
