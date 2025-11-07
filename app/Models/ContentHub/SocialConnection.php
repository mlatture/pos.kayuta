<?php

namespace App\Models\ContentHub;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class SocialConnection extends Model
{
    protected $fillable = [
        'channel','account_name','account_id',
        'access_token','refresh_token','token_expires_at',
        'is_active','last_health_check','health_status','connection_metadata'
    ];

    protected $casts = [
        'token_expires_at'   => 'datetime',
        'last_health_check'  => 'datetime',
        'is_active'          => 'boolean',
        'connection_metadata'=> 'array',
    ];

    // Encrypt tokens
    public function setAccessTokenAttribute($value){
        $this->attributes['access_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getAccessTokenAttribute($value){
        return $value ? Crypt::decryptString($value) : null;
    }
    public function setRefreshTokenAttribute($value){
        $this->attributes['refresh_token'] = $value ? Crypt::encryptString($value) : null;
    }
    public function getRefreshTokenAttribute($value){
        return $value ? Crypt::decryptString($value) : null;
    }
}
