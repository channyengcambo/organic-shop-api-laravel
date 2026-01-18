<?php

namespace App\Http\Middleware;

use App\Services\Auth\JwtService;
use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class JwtAuthMiddleware
{
    public function __construct(
        protected JwtService $jwtService
    )
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $decoded = $this->jwtService->decodeToken($token);

            if (!$decoded) {
                return response()->json(['error' => 'Invalid or expired token'], 401);
            }

            $userId = $decoded->data->user_id;

            $user = User::find($userId);

            if (!$user) {
                return response()->json(['error' => 'User not found'], 401);
            }

            // 🔥 THIS IS THE KEY LINE
            Auth::setUser($user);

        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Token parsing error',
                'message' => $e->getMessage(),
            ], 401);
        }

        return $next($request);
    }
}
