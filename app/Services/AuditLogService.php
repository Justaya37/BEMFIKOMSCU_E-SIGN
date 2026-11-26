<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Letter;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogService
{
    /**
     * Log an audit action for a letter.
     */
    public static function log(
        string $action,
        ?Letter $letter = null,
        ?User $user = null,
        ?array $details = null,
        ?Request $request = null
    ): AuditLog {
        $ip = null;
        $userAgent = null;

        if ($request) {
            $ip = $request->ip();
            $userAgent = $request->userAgent();
        } elseif (request()) {
            $ip = request()->ip();
            $userAgent = request()->userAgent();
        }

        return AuditLog::create([
            'letter_id' => $letter?->id,
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'ip_address' => $ip,
            'user_agent' => $userAgent ? substr($userAgent, 0, 500) : null,
            'details' => $details ? json_encode($details, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : null,
            'created_at' => now(),
        ]);
    }
}
