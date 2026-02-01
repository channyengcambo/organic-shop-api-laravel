<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:5173');
        $response->headers->set(
            'Access-Control-Allow-Headers',
            'Content-Type, Authorization, X-App-Key, Accept'
        );
        $response->headers->set(
            'Access-Control-Allow-Methods',
            'GET, POST, PUT, PATCH, DELETE, OPTIONS'
        );
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Preflight
        if ($request->isMethod('OPTIONS')) {
            return response()->noContent(204)->withHeaders($response->headers->all());
        }

        return $response;
    }
}
