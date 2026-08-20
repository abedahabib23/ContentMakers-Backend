<?php

use App\Http\Controllers\Api\ProjectController;
use Illuminate\Support\Facades\Route;

// Authorization here is via ProjectPolicy/Gate::authorize() in each
// controller action, not the `permission` middleware.
Route::middleware('auth:api')->prefix('projects')->group(function () {
    Route::get('/', [ProjectController::class, 'index']);
    Route::post('/', [ProjectController::class, 'store']);
    Route::get('{project}', [ProjectController::class, 'show']);
    Route::put('{project}', [ProjectController::class, 'update']);
    Route::delete('{project}', [ProjectController::class, 'destroy']);
});
