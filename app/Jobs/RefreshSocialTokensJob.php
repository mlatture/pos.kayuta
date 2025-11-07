<?php

namespace App\Jobs;

use App\Models\ContentHub\SocialConnection;
use App\Services\Social\SocialProviderService;
use App\Support\SystemLogger;

class RefreshSocialTokensJob extends Job
{
    public function handle(SocialProviderService $svc): void
    {
        SocialConnection::query()->get()->each(function ($conn) use ($svc) {
            try {
                if ($svc->needsRefresh($conn)) {
                    $ok = $svc->refreshToken($conn);
                    // if ($ok) {
                    //     SystemLogger::log('token_refresh', [
                    //         'provider'=>$conn->channel,
                    //         'account_id'=>$conn->account_id,
                    //     ]);
                    // }
                }
                // Optionally: ping a trivial “me” endpoint to set health_status
            } catch (\Throwable $e) {
                $conn->update(['health_status' => 'error']);
                // SystemLogger::log('connection_error', [
                //     'provider'=>$conn->channel,
                //     'account_id'=>$conn->account_id,
                //     'error'=>$e->getMessage(),
                // ]);
            }
        });
    }
}
