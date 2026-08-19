<?php

namespace App\Exceptions\Auth;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvalidCredentialsException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(__('auth.failed'), 401);
    }
}
