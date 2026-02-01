<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Support\Facades\Log;

class PublicApiKeyMiddleware
{
    use ApiResponse;

    public function handle($request, Closure $next)
    {
        // Let CORS middleware handle OPTIONS
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent(204);
        }

        $apiKey = $request->header('X-APP-KEY');

        Log::info('Public API request', [
            'api_key' => $apiKey,
            'path' => $request->path(),
            'method' => $request->method(),
            'ip' => $request->ip(),
        ]);

        if (!$apiKey || !hash_equals(config('app.public_api_key'), $apiKey)) {
            return $this->errorResponse('Invalid public API key', 403);
        }

        return $next($request);
    }
}
