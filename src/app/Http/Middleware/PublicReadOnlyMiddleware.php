<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicReadOnlyMiddleware
{
    public function handle($request, $next)
    {
        if (!in_array($request->method(), ['GET', 'HEAD', 'OPTIONS'])) {
            return response()->json(['message' => 'Method not allowed'], 405);
        }

        return $next($request);
    }
}
