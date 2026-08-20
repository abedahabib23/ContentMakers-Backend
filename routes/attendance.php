<?php

use App\Http\Controllers\Api\AttendanceController;
use Illuminate\Support\Facades\Route;

// Delegated to the session's parent project's ProjectPolicy, same as
// training sessions and registration forms.
Route::middleware('auth:api')->group(function () {
    Route::get('sessions/{session}/attendances', [AttendanceController::class, 'index']);
    Route::post('sessions/{session}/attendances', [AttendanceController::class, 'store']);

    Route::get('attendances/{attendance}', [AttendanceController::class, 'show']);
    Route::put('attendances/{attendance}', [AttendanceController::class, 'update']);
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy']);
});
