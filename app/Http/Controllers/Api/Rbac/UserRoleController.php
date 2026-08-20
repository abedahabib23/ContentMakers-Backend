<?php

namespace App\Http\Controllers\Api\Rbac;

use App\Http\Controllers\Controller;
use App\Http\Requests\Rbac\SyncUserRolesRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    public function update(SyncUserRolesRequest $request, User $user): JsonResponse
    {
        Gate::authorize('assign', Role::class);

        $user->syncRoles($request->validated('role_ids'));

        return ApiResponse::success(new UserResource($user->load('roles.permissions')), __('rbac.user_roles_synced'));
    }
}
