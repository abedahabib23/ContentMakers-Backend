<?php

use App\Http\Controllers\Api\PublicRegistrationController;
use App\Http\Controllers\Api\RegistrationFormController;
use App\Http\Controllers\Api\RegistrationSubmissionController;
use Illuminate\Support\Facades\Route;

// Authenticated — form management, delegated to ProjectPolicy (same as
// training sessions: if you can manage the project, you can manage its
// registration forms).
Route::middleware('auth:api')->group(function () {
    Route::get('projects/{project}/registration-forms', [RegistrationFormController::class, 'index']);
    Route::post('projects/{project}/registration-forms', [RegistrationFormController::class, 'store']);
    Route::get('registration-forms/{form}/submissions', [RegistrationFormController::class, 'submissions']);

    Route::get('registration-submissions/{submission}/id-photo', [RegistrationSubmissionController::class, 'idPhoto']);
    Route::get('registration-submissions/{submission}/cv', [RegistrationSubmissionController::class, 'cv']);
});

// Public — no auth:api. Candidates reach these via the registration_url
// handed back from the store() call above, not by logging in.
Route::prefix('register')->group(function () {
    Route::get('{form:slug}', [PublicRegistrationController::class, 'show']);
    Route::post('{form:slug}', [PublicRegistrationController::class, 'store'])
        ->middleware('throttle:10,1,registration-submit');
});
