<?php

namespace App\Http\Controllers\Api\Rbac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rbac\SyncUserRolesRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Rbac\RoleService;
use Illuminate\Http\JsonResponse;

class UserRoleController extends Controller
{
    public function __construct(private readonly RoleService $roleService) {}

    public function update(SyncUserRolesRequest $request, User $user): JsonResponse
    {
        $user = $this->roleService->syncUserRoles($user, $request->validated('role_ids'));

        return ApiResponse::success(new UserResource($user), __('rbac.user_roles_synced'));
    }
}
