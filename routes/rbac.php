<?php

use App\Http\Controllers\Api\Rbac\PermissionController;
use App\Http\Controllers\Api\Rbac\RoleController;
use App\Http\Controllers\Api\Rbac\UserRoleController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::get('permissions', [PermissionController::class, 'index'])
        ->middleware('permission:permissions.view');

    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:roles.view');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:roles.create');
        Route::get('{role}', [RoleController::class, 'show'])->middleware('permission:roles.view');
        Route::put('{role}', [RoleController::class, 'update'])->middleware('permission:roles.update');
        Route::delete('{role}', [RoleController::class, 'destroy'])->middleware('permission:roles.delete');
    });

    Route::put('users/{user}/roles', [UserRoleController::class, 'update'])
        ->middleware('permission:roles.assign');
});
