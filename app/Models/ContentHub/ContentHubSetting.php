<?php

namespace App\Models\ContentHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class ContentHubSetting extends Model
{
    protected $fillable = [
        'is_enabled','ai_service_provider','ai_api_credentials',
        'default_publish_delay_minutes','auto_publish_after_approvals',
        'face_blur_enabled','profanity_filter_enabled','guest_uploads_enabled',
        'max_media_per_batch','settings_json'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'face_blur_enabled' => 'boolean',
        'profanity_filter_enabled' => 'boolean',
        'guest_uploads_enabled' => 'boolean',
        'settings_json' => 'array', // <-- IMPORTANT
    ];

    public function setAiApiCredentialsAttribute($value){
        $this->attributes['ai_api_credentials'] = $value
            ? Crypt::encryptString(is_string($value) ? $value : json_encode($value))
            : null;
    }

    public function getAiApiCredentialsAttribute($value){
        return $value ? json_decode(Crypt::decryptString($value), true) : null;
    }
}
