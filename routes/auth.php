<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Laravel's generic throttle:max,minutes middleware keys its bucket by
    // domain+IP ONLY when unauthenticated — with no 3rd "prefix" argument,
    // every unnamed route sharing this signature would share ONE counter,
    // so e.g. calling /login a few times would eat into /refresh's quota
    // too. The prefix argument namespaces each route its own bucket.
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1,login');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('throttle:20,1,refresh');

    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:3,1,forgot-password');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:5,1,reset-password');

    Route::get('verify-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('email/resend', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1,email-resend');
    });
});
