<?php

use App\Http\Controllers\Api\TrainerFileController;
use Illuminate\Support\Facades\Route;

// Authorization here is via TrainerPolicy/Gate::authorize() in the
// controller action, not the `permission` middleware.
Route::middleware('auth:api')->prefix('trainers')->group(function () {
    Route::post('{trainer}/files', [TrainerFileController::class, 'update']);
    Route::get('{trainer}/id-photo', [TrainerFileController::class, 'idPhoto']);
    Route::get('{trainer}/cv', [TrainerFileController::class, 'cv']);
    Route::get('{trainer}/certificates/{index}', [TrainerFileController::class, 'certificate'])->whereNumber('index');
});
