<?php

namespace App\Http\Middleware;

use App\Jobs\StoreAuditLogJob;
use App\Services\Audit\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $start = microtime(true);

        $response = $next($request);

        $duration = (microtime(true) - $start) * 1000;

        $logData = [
            'user_id' => optional($request->user())->id,
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'route' => optional($request->route())->getName(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'request_payload' => AuditLogger::sanitize($request->all()),
            'response_payload' => method_exists($response, 'getContent')
                ? json_decode($response->getContent(), true)
                : null,
            'status_code' => $response->status(),
            'duration_ms' => round($duration),
        ];

        AuditLogger::logToFile($logData);
        StoreAuditLogJob::dispatch($logData)
            ->onQueue('audit');

        return $response;
    }
}

