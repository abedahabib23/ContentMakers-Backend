<?php

namespace App\Exceptions\Registration;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationClosedException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(__('registration.closed'), 422);
    }
}
