<?php

namespace App\Services\Auth;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Random\RandomException;

class JwtService
{
    protected $secret;

    public function __construct()
    {
        $this->secret = env('JWT_SECRET', 'default-secret');
    }

    /**
     * Generate Access Token (Short-lived)
     */
    public function generateToken(array $data, int $expiry = 60): string
    {
        $issuedAt = time();
        $expire = $issuedAt + ($expiry * 60);

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expire,
            'data' => $data
        ];

        return JWT::encode($payload, $this->secret, 'HS256');
    }

    /**
     * Generate a refresh token for a user and store it in cache
     * @throws RandomException
     */
    public function generateRefreshToken(User $user, Request $request, int $ttlSeconds = 604800): string
    {
        $refreshToken = bin2hex(random_bytes(40));

        $sessionData = [
            'user_id' => $user->id,
            'ip' => $request->ip(),
            'device' => $request->header('User-Agent') ?? 'unknown',
            'created_at' => now()->toDateTimeString(),
        ];

        // Key = token, Value = session info JSON
        Redis::setex(
            "user_refresh_token:{$refreshToken}",
            $ttlSeconds,
            json_encode($sessionData)
        );

        Redis::sadd("user_refresh_tokens:{$user->id}", $refreshToken);

        return $refreshToken;
    }

    /**
     * Decode and Validate Token
     */
    public function decodeToken(string $token): ?object
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            return $decoded;
        } catch (Exception $e) {
            return null;
        }
    }
}
