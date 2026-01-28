<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FrontEndPath\MainHeroController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\FrontEndPath\NavigationMenuItemsController;

Route::middleware(['audit', 'public.key', 'throttle:60,1'])->group(function () {
    //Admin
    Route::prefix('v1')->group(function () {
        // Public Route
        Route::post('/auth/login', [AuthController::class, 'login']);

        // Protected Route (Requires JWT Token)
        Route::middleware(['jwt.auth'])->group(function () {
//            Admin Role
            Route::middleware(['role:ADMIN'])->group(function () {
                Route::delete("/navigation-menu-items/{id}", [NavigationMenuItemsController::class, 'destroy']);
                Route::delete("/main-heroes/{id}", [MainHeroController::class, 'destroy']);
            });

//            Role Manager
            Route::middleware(['role:MANAGER,ADMIN'])->group(function () {
                Route::put("/navigation-menu-items/{id}", [NavigationMenuItemsController::class, 'update']);
                Route::post("/main-heroes/{id}", [MainHeroController::class, 'updateMainHero']);
            });

//            Role Staff
            Route::middleware(['role:ADMIN,MANAGER,STAFF'])->group(function () {
                // Navigation Menu
                Route::post("/navigation-menu-items", [NavigationMenuItemsController::class, 'store']);
                Route::post('/navigation-menu-items/{parentId}/child', [NavigationMenuItemsController::class, 'storeChild']);

                //    Main Hero
                Route::post("/main-heroes", [MainHeroController::class, 'store']);
            });
//        Auth
            Route::post('/auth/logout', [AuthController::class, 'logout']);
            Route::post('/auth/refresh', [AuthController::class, 'refresh']);
            Route::post('/auth/log-out', [AuthController::class, 'logout']);
            Route::post('/auth/log-out-all', [AuthController::class, 'logoutAll']);
            Route::get('/me', [UserController::class, 'me']);
        });
    });

//Public
    Route::prefix('v1/front-end-path')
        ->middleware(['public.readonly', 'public.key', 'api'])
        ->group(function () {
            Route::get('/navigation-menu-items', [NavigationMenuItemsController::class, 'index']);
            Route::get('/main-heroes/list', [MainHeroController::class, 'getAllPublicMainHeroes']);
        });
});
