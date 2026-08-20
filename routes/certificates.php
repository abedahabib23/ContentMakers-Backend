<?php

use App\Http\Controllers\Api\CertificateController;
use Illuminate\Support\Facades\Route;

// Authorization is via CertificatePolicy — issuing/managing is a
// project-management action, viewing is also open to the trainee it
// belongs to.
Route::middleware('auth:api')->group(function () {
    Route::get('trainees/{trainee}/certificates', [CertificateController::class, 'index']);
    Route::post('trainees/{trainee}/certificates', [CertificateController::class, 'store']);

    Route::get('certificates/{certificate}', [CertificateController::class, 'show']);
    Route::put('certificates/{certificate}', [CertificateController::class, 'update']);
    Route::delete('certificates/{certificate}', [CertificateController::class, 'destroy']);
});
