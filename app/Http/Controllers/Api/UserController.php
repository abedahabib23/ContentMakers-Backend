<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Users\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(): JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $users = $this->userService->list();

        return ApiResponse::success(
            UserResource::collection($users),
            __('users.listed'),
            200,
            [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        Gate::authorize('create', User::class);

        $user = $this->userService->create($request->validated());

        return ApiResponse::success(new UserResource($user), __('users.created'), 201);
    }

    public function show(User $user): JsonResponse
    {
        Gate::authorize('view', $user);

        return ApiResponse::success(new UserResource($user), __('users.retrieved'));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        Gate::authorize('update', $user);

        $user = $this->userService->update($user, $request->validated());

        return ApiResponse::success(new UserResource($user), __('users.updated'));
    }

    public function destroy(User $user): JsonResponse
    {
        Gate::authorize('delete', $user);

        $this->userService->delete($user);

        return ApiResponse::success(null, __('users.deleted'));
    }
}
