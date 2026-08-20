<?php

namespace App\Exceptions\Rbac;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleInUseException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(__('rbac.role_in_use'), 409);
    }
}
