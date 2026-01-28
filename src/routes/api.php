<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FrontEndPath\MainHeroController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FrontEndPath\NavigationMenuItemsController;

Route::middleware(['audit'])->group(function () {
    //Admin
    Route::prefix('v1')->group(function () {
        // Public Route
        Route::post('/auth/login', [AuthController::class, 'login']);

        // Protected Route (Requires JWT Token)
        Route::middleware(['jwt.auth', 'audit'])->group(function () {

//            Admin Role
            Route::middleware(['role:ADMIN'])->group(function () {
                Route::delete("/navigation-menu-items/{id}", [NavigationMenuItemsController::class, 'destroy']);
                Route::delete("/main-heroes/{id}", [MainHeroController::class, 'destroy']);
            });
//        Auth
            Route::post('/auth/logout', [AuthController::class, 'logout']);
            Route::post('/auth/refresh', [AuthController::class, 'refresh']);
            Route::post('/auth/log-out', [AuthController::class, 'logout']);
            Route::post('/auth/log-out-all', [AuthController::class, 'logoutAll']);

//        User
            Route::get('/me', [UserController::class, 'me']);

//        Navigation Menu
            Route::post("/navigation-menu-items", [NavigationMenuItemsController::class, 'store']);
            Route::post('/navigation-menu-items/{parentId}/child', [NavigationMenuItemsController::class, 'storeChild']);
            Route::put("/navigation-menu-items/{id}", [NavigationMenuItemsController::class, 'update']);
        });

//    Main Hero
        Route::post("/main-heroes", [MainHeroController::class, 'store']);
        Route::post("/main-heroes/{id}", [MainHeroController::class, 'updateMainHero']);
    });

//Public
    Route::prefix('v1/front-end-path')->group(function () {
        Route::get('/navigation-menu-items', [NavigationMenuItemsController::class, 'index']);
        Route::get('/main-heroes/list', [MainHeroController::class, 'getAllPublicMainHeroes']);
    });
});
