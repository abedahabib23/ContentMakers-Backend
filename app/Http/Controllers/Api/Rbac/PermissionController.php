<?php

namespace App\Http\Controllers\Api\Rbac;

use App\Http\Controllers\Controller;
use App\Http\Resources\PermissionResource;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        // Permissions are only ever viewed in the context of editing a
        // role's checkboxes, so this reuses Role's viewAny ability rather
        // than defining one of its own.
        Gate::authorize('viewAny', Role::class);

        return ApiResponse::success(
            PermissionResource::collection(Permission::orderBy('name')->get()),
            __('rbac.permissions_listed'),
        );
    }
}
