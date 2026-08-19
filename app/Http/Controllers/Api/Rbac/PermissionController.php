<?php

namespace App\Http\Controllers\Api\Rbac;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Responses\ApiResponse;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(
            PermissionResource::collection(Permission::orderBy('name')->get()),
            __('rbac.permissions_listed'),
        );
    }
}
