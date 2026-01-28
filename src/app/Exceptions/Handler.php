<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Illuminate\Auth\Access\AuthorizationException;
use App\Enums\HttpStatus;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    public function render($request, Throwable $e): \Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            // 401 - Unauthenticated
            if ($e instanceof AuthenticationException) {
                return $this->errorResponse(
                    'Unauthenticated',
                    null,
                    HttpStatus::UNAUTHORIZED->value
                );
            }

            // 403 - Forbidden
            if ($e instanceof AuthorizationException) {
                return $this->errorResponse(
                    'You are not allowed to perform this action',
                    null,
                    HttpStatus::FORBIDDEN->value
                );
            }

            // 422 - Validation Error
            if ($e instanceof ValidationException) {
                return $this->errorResponse(
                    'Validation failed',
                    $e->errors(),
                    HttpStatus::UNPROCESSABLE_ENTITY->value
                );
            }

            // 429 - Too many request
            if ($e instanceof TooManyRequestsHttpException) {
                return $this->errorResponse(
                    'Too many requests!',
                    $e->getMessage(),
                    HttpStatus::TOO_MANY_REQUESTS->value
                );
            }

            // 404 - Not Found
            if ($e instanceof NotFoundHttpException) {
                return $this->errorResponse(
                    'Resource not found',
                    null,
                    HttpStatus::NOT_FOUND->value
                );
            }

            // 405 - Method Not Allowed
            if ($e instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse(
                    'HTTP method not allowed',
                    null,
                    HttpStatus::METHOD_NOT_ALLOWED->value
                );
            }

            // Other HTTP exceptions
            if ($e instanceof HttpExceptionInterface) {
                return $this->errorResponse(
                    $e->getMessage() ?: 'HTTP error',
                    null,
                    $e->getStatusCode()
                );
            }

            // 500 - Internal Server Error (Hide message in production)
            return $this->errorResponse(
                app()->isProduction()
                    ? 'Internal server error'
                    : $e->getMessage(),
                null,
                HttpStatus::INTERNAL_SERVER_ERROR->value
            );
        }

        return parent::render($request, $e);
    }
}
