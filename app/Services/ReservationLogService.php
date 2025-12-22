<?php

namespace App\Services;

use App\Models\ReservationLog;
use Illuminate\Support\Facades\Auth;

class ReservationLogService
{
    /**
     * Log an event for a reservation.
     *
     * @param int|string $reservationId
     * @param string $eventType
     * @param mixed $oldValue
     * @param mixed $newValue
     * @param string|null $comment
     * @return ReservationLog
     */
    public function log($reservationId, string $eventType, $oldValue = null, $newValue = null, ?string $comment = null)
    {
        // Extract IP safely
        $ip = request()->ip();

        // Use Auth::id() if available, otherwise null
        $userId = Auth::id();

        return ReservationLog::create([
            'reservation_id' => $reservationId,
            'user_id' => $userId,
            'event_type' => $eventType,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'comment' => $comment,
            'ip_address' => $ip,
        ]);
    }
}
