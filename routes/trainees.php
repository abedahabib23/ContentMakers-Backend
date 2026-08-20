<?php

use App\Http\Controllers\Api\TraineeController;
use Illuminate\Support\Facades\Route;

// Authorization is via TraineePolicy — a trainer sees their own roster
// without any permission needed, trainees.view opens it up further.
Route::middleware('auth:api')->group(function () {
    Route::get('trainees', [TraineeController::class, 'index']);
    Route::get('trainees/{trainee}', [TraineeController::class, 'show']);
});
