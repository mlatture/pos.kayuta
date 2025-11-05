<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

class SystemLogger
{
    public static function log(string $type, array $data = [], ?int $adminId = null): void
    {
        DB::table('system_logs')->insert([
            'type'       => $type,             // e.g. content_setup, ai_config, settings_update
            'payload'    => json_encode($data),
            'actor_id'   => $adminId,
            'created_at' => now(),
        ]);
    }
}
