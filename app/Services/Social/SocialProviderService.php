<?php

namespace App\Services\Social;

use App\Models\ContentHub\SocialConnection;
use Carbon\Carbon;

class SocialProviderService
{
    public static function scopesFor(string $provider): array
    {
        return match($provider) {
            // 'facebook' => ['pages_manage_posts','pages_read_engagement','pages_show_list','instagram_basic','instagram_content_publish'],
            // 'tiktok'   => ['user.info.basic','video.publish','video.upload'],
            // 'pinterest'=> ['boards:read','pins:write','boards:write'],
            'google'   => ['https://www.googleapis.com/auth/business.manage'],
            default    => [],
        };
    }

    /**
     * Persist tokens & basic metadata
     * $socialUser is Socialite user: ->token, ->refreshToken, ->expiresIn, ->id, ->name, ->avatar, ->email
     */
    public function storeOrUpdateConnection(string $provider, $socialUser): SocialConnection
    {
        // Normalize metadata (account/page id/name can vary; enhance per provider later)
        $accountId   = $socialUser->id;
        $accountName = $socialUser->name ?? $socialUser->nickname ?? $provider.'-account';
        $expiresAt   = isset($socialUser->expiresIn) ? now()->addSeconds($socialUser->expiresIn) : null;


    if($provider == 'google')
    {
        $realProviderName = 'google_business';
    }
        return SocialConnection::updateOrCreate(
            ['channel' => $realProviderName, 'account_id' => $accountId],
            [
                'account_name'      => $accountName,
                'access_token'      => $socialUser->token ?? null,
                'refresh_token'     => $socialUser->refreshToken ?? null,
                'token_expires_at'  => $expiresAt,
                'is_active'         => true,
                'health_status'     => 'healthy',
                'connection_metadata'=> [
                    'avatar' => $socialUser->avatar ?? null,
                    'email'  => $socialUser->email ?? null,
                ],
            ]
        );
    }

    public function needsRefresh(?SocialConnection $conn): bool
    {
        if (!$conn || !$conn->token_expires_at) return false;
        return Carbon::parse($conn->token_expires_at)->isBefore(now()->addMinutes(10));
    }

    // TODO: per provider refresh flows (Google supports refresh_token; Meta often uses long-lived tokens)
    public function refreshToken(SocialConnection $conn): bool
    {
        // implement per-provider token refresh (call provider API).
        // On success:
        // $conn->access_token = $newToken;
        // $conn->token_expires_at = now()->addSeconds($ttl);
        // $conn->save();
        return false;
    }
}
