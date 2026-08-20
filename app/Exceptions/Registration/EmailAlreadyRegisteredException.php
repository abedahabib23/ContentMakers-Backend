<?php

namespace App\Exceptions\Registration;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailAlreadyRegisteredException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(__('registration.email_already_registered'), 409);
    }
}
