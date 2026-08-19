<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Every API response goes through here so the envelope shape
 * ({status, message, data, meta, errors}) never drifts between controllers.
 */
final class ApiResponse
{
    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        ?array $meta = null,
    ): JsonResponse {
        $payload = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    /**
     * @param  array<string, array<int, string>>|null  $errors
     */
    public static function error(
        string $message,
        int $status = 400,
        ?array $errors = null,
    ): JsonResponse {
        $payload = [
            'status' => 'error',
            'message' => $message,
            'data' => null,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return response()->json($payload, $status);
    }
}
