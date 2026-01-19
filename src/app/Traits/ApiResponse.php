<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    protected function successResponse(
        mixed  $data = null,
        string $message = 'Success',
        int    $statusCode = 200,
        array  $meta = []
    ): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
            'meta' => $meta,
        ], $statusCode);
    }

    protected function successResponseNoData(
        string $message = 'Success',
        int    $statusCode = 200,
        array  $meta = []
    ): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'errors' => null,
            'meta' => $meta,
        ], $statusCode);
    }

    protected function errorResponse(
        string $message = 'Something went wrong',
        mixed  $errors = null,
        int    $statusCode = 400
    ): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'meta' => null,
        ], $statusCode);
    }
}
