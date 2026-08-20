<?php

use App\Http\Controllers\Api\CenterSettingController;
use Illuminate\Support\Facades\Route;

// A singleton resource — no {id}, always operates on
// CenterSetting::current(). Gated by CenterSettingPolicy.
Route::middleware('auth:api')->group(function () {
    Route::get('center-settings', [CenterSettingController::class, 'show']);
    Route::put('center-settings', [CenterSettingController::class, 'update']);
});
