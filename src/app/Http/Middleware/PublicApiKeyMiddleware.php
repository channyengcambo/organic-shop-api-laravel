<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;

class PublicApiKeyMiddleware
{
    use ApiResponse;

    public function handle($request, Closure $next)
    {
        $apiKey = $request->header('X-APP-KEY');

        if ($apiKey !== config('app.public_api_key')) {
            return $this->errorResponse(
                'Invalid public API key',
                403
            );
        }

        return $next($request);
    }
}
