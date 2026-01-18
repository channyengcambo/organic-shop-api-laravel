<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    public function render($request, Throwable $exception): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        // Check if request expects JSON (API route)
        if ($request->expectsJson() || $request->is('api/*')) {

            // Authentication exception
            if ($exception instanceof AuthenticationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            // Validation exception
            if ($exception instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $exception->errors(),
                ], 422);
            }

            // Custom exceptions or others
            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500);
        }

        // For non-API routes, keep default HTML response
        return parent::render($request, $exception);
    }
}
