<?php

namespace App\Services\Audit;

use App\Models\Audit\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogger
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function sanitize(array $data): array
    {
        return collect($data)->except([
            'password',
            'password_confirmation',
            'token',
            'access_token',
            'refresh_token',
        ])->toArray();
    }

    public static function logToFile(array $data): void
    {
        Log::channel('audit')->info('API AUDIT', $data);
    }

    public static function logToDatabase(array $data): void
    {
        AuditLog::create($data);
    }
}
