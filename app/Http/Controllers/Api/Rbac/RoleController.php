<?php

namespace App\Http\Controllers\Api\Rbac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rbac\StoreRoleRequest;
use App\Http\Requests\Rbac\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Http\Responses\ApiResponse;
use App\Models\Role;
use App\Services\Rbac\RoleService;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService) {}

    public function index(): JsonResponse
    {
        return ApiResponse::success(
            RoleResource::collection($this->roleService->list()),
            __('rbac.roles_listed'),
        );
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());

        return ApiResponse::success(new RoleResource($role), __('rbac.role_created'), 201);
    }

    public function show(Role $role): JsonResponse
    {
        return ApiResponse::success(new RoleResource($role->load('permissions')), __('rbac.role_retrieved'));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->update($role, $request->validated());

        return ApiResponse::success(new RoleResource($role), __('rbac.role_updated'));
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->roleService->delete($role);

        return ApiResponse::success(null, __('rbac.role_deleted'));
    }
}
