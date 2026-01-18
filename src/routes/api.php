<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Public Route
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Protected Route (Requires JWT Token)
    Route::middleware(['jwt.auth'])->group(function () {
//        Auth
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::post('/auth/refresh', [AuthController::class, 'refresh']);
        Route::post('/auth/log-out', [AuthController::class, 'logout']);
        Route::post('/auth/log-out-all', [AuthController::class, 'logoutAll']);

//        User
        Route::get('/me', [UserController::class, 'me']);


    });
});
