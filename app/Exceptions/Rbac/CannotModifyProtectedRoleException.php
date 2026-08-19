<?php

namespace App\Exceptions\Rbac;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CannotModifyProtectedRoleException extends Exception
{
    public function render(Request $request): JsonResponse
    {
        return ApiResponse::error(__('rbac.cannot_modify_super_admin'), 403);
    }
}
