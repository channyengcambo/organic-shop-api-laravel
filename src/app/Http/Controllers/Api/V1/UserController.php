<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    )
    {
    }

    public function me(Request $request)
    {
        $user = $this->userService->me(Auth::user());

        return response()->json([
            'message' => 'You are authenticated!',
            'data' => new UserResource($user),
        ]);
    }

}
