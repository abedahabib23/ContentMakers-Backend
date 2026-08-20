<?php

use App\Http\Controllers\Api\TrainingSessionController;
use Illuminate\Support\Facades\Route;

// A session's authorization is entirely delegated to its parent project's
// ProjectPolicy (view for reads, update for writes) — there's no separate
// sessions.* permission module.
Route::middleware('auth:api')->group(function () {
    Route::get('projects/{project}/sessions', [TrainingSessionController::class, 'index']);
    Route::post('projects/{project}/sessions', [TrainingSessionController::class, 'store']);

    Route::get('sessions/{session}', [TrainingSessionController::class, 'show']);
    Route::put('sessions/{session}', [TrainingSessionController::class, 'update']);
    Route::delete('sessions/{session}', [TrainingSessionController::class, 'destroy']);
});
