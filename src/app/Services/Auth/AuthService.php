<?php

namespace App\Services\Auth;

use App\Models\User;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

//    Login
    public function login(array $credentials, Request $request): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        // --- ADD THIS COMMENT BELOW ---
        /** @var User $user */
        // -----------------------------

        $user->load('roles');


        // 2. Generate Access Token (Payload: user_id, role)
        // Expires in 1 hour (60 mins)
        $accessToken = $this->jwtService->generateToken([
            'user_id' => $user->id,
            'username' => $user->username
        ], 60);

        $refreshToken = $this->jwtService->generateRefreshToken($user, $request);

        // 5. Revoke old sessions/tokens if you want (Single session logic)

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'user' => $user
        ];
    }

//    Refresh
    public function refresh(string $token): array
    {
        $sessionJson = Redis::get("user_refresh_token:{$token}");

        if (!$sessionJson) {
            throw new Exception("Invalid or expired refresh token");
        }

        $sessionData = json_decode($sessionJson, true);

        $user = User::find($sessionData['user_id']);
        if (!$user) {
            throw new AuthenticationException("User not found for this refresh token");
        }

        // Delete old refresh token for rotation
        Redis::del("user_refresh_token:{$token}");

        // Generate new access token
        $accessToken = $this->jwtService->generateToken([
            'user_id' => $user->id,
            'username' => $user->username,
            'roles' => $user->roles->pluck('name')->toArray(),
        ]);

        // Generate new refresh token
        $newRefreshToken = $this->jwtService->generateRefreshToken($user, request());

        return [
            'access_token' => $accessToken,
            'refresh_token' => $newRefreshToken,
            'token_type' => 'bearer',
            'user' => $user,
        ];
    }

//    Logout
    public function logout(string $refreshToken): bool
    {
        // Delete only this token
        return Redis::del("user_refresh_token:{$refreshToken}") > 0;
    }

//    Logout All
    public function logoutAll(User $user): int
    {
        $userTokens = Redis::smembers("user_refresh_tokens:{$user->id}");
        $deleted = 0;

        foreach ($userTokens as $token) {
            Redis::del("user_refresh_token:{$token}");
            $deleted++;
        }

        // Remove the user-specific set
        Redis::del("user_refresh_tokens:{$user->id}");

        return $deleted;
    }
}
