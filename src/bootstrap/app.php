<?php

use App\Http\Middleware\AuditLogMiddleware;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\JwtAuthMiddleware;
use App\Http\Middleware\PublicReadOnlyMiddleware;
use App\Http\Middleware\PublicApiKeyMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'jwt.auth' => JwtAuthMiddleware::class,
            'audit' => AuditLogMiddleware::class,
            'role' => RoleMiddleware::class,
            'public.readonly' => PublicReadOnlyMiddleware::class,
            'public.key' => PublicApiKeyMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
