<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// Authorization here is via UserPolicy/Gate::authorize() in each
// controller action, not the `permission` middleware.
Route::middleware('auth:api')->prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{user}', [UserController::class, 'show']);
    Route::put('{user}', [UserController::class, 'update']);
    Route::delete('{user}', [UserController::class, 'destroy']);
});
