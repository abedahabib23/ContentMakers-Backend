<?php

use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskEvaluationController;
use Illuminate\Support\Facades\Route;

// Delegated to the parent project's ProjectPolicy, same as sessions,
// registration forms, and attendance.
Route::middleware('auth:api')->group(function () {
    Route::get('projects/{project}/tasks', [TaskController::class, 'index']);
    Route::post('projects/{project}/tasks', [TaskController::class, 'store']);

    Route::get('tasks/{task}', [TaskController::class, 'show']);
    Route::put('tasks/{task}', [TaskController::class, 'update']);
    Route::delete('tasks/{task}', [TaskController::class, 'destroy']);

    Route::get('tasks/{task}/evaluations', [TaskEvaluationController::class, 'index']);
    Route::post('tasks/{task}/evaluations', [TaskEvaluationController::class, 'store']);

    Route::get('task-evaluations/{evaluation}', [TaskEvaluationController::class, 'show']);
    Route::put('task-evaluations/{evaluation}', [TaskEvaluationController::class, 'update']);
    Route::delete('task-evaluations/{evaluation}', [TaskEvaluationController::class, 'destroy']);
});
