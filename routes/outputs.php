<?php

use App\Http\Controllers\Api\TraineeOutputController;
use Illuminate\Support\Facades\Route;

// Authorization is via TraineeOutputPolicy — the trainee manages their
// own content, the project's trainer reviews (status + score), both via
// Gate::allows() in each Request's authorize().
Route::middleware('auth:api')->group(function () {
    Route::get('trainees/{trainee}/outputs', [TraineeOutputController::class, 'index']);
    Route::post('trainees/{trainee}/outputs', [TraineeOutputController::class, 'store']);

    Route::get('trainee-outputs/{output}', [TraineeOutputController::class, 'show']);
    Route::put('trainee-outputs/{output}', [TraineeOutputController::class, 'update']);
    Route::put('trainee-outputs/{output}/review', [TraineeOutputController::class, 'review']);
    Route::delete('trainee-outputs/{output}', [TraineeOutputController::class, 'destroy']);
});
