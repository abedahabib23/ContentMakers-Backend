<?php

use App\Http\Controllers\Api\Rbac\PermissionController;
use App\Http\Controllers\Api\Rbac\RoleController;
use App\Http\Controllers\Api\Rbac\UserRoleController;
use Illuminate\Support\Facades\Route;

// Authorization here is via RolePolicy/Gate::authorize() in each
// controller action, not the `permission` middleware.
Route::middleware('auth:api')->group(function () {
    Route::get('permissions', [PermissionController::class, 'index']);

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index']);
        Route::post('/', [RoleController::class, 'store']);
        Route::get('{role}', [RoleController::class, 'show']);
        Route::put('{role}', [RoleController::class, 'update']);
        Route::delete('{role}', [RoleController::class, 'destroy']);
    });

    Route::put('users/{user}/roles', [UserRoleController::class, 'update']);
});
