<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\Auth\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $userRequest)
    {
        $result = $this->authService->login($userRequest->only('username', 'password'), $userRequest);

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'access_token' => $result['access_token'],
                'refresh_token' => $result['refresh_token'],
                'token_type' => 'bearer',
                'user' => new UserResource($result['user'])
            ]
        ]);
    }

    public function refresh(Request $request)
    {
        $token = $request->header('X-Refresh-Token');

        try {
            $result = $this->authService->refresh($token);

            return response()->json([
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $result['access_token'],
                    'refresh_token' => $result['refresh_token'],
                    'token_type' => 'bearer',
                    'user' => new UserResource($result['user']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->header('X-Refresh-Token');
        $this->authService->logout($token);

        return response()->json([
            'message' => 'Logged out from this device.'
        ]);
    }

    public function logoutAll(Request $request)
    {
        $deleted = $this->authService->logoutAll(Auth::user());

        return response()->json([
            'message' => "Logged out from $deleted devices."
        ]);
    }
}
