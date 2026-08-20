<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CenterSettings\UpdateCenterSettingRequest;
use App\Http\Resources\CenterSettingResource;
use App\Http\Responses\ApiResponse;
use App\Models\CenterSetting;
use App\Services\CenterSettings\CenterSettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CenterSettingController extends Controller
{
    public function __construct(private readonly CenterSettingService $settingService) {}

    public function show(): JsonResponse
    {
        Gate::authorize('view', CenterSetting::class);

        return ApiResponse::success(new CenterSettingResource(CenterSetting::current()), __('center_settings.retrieved'));
    }

    public function update(UpdateCenterSettingRequest $request): JsonResponse
    {
        Gate::authorize('update', CenterSetting::class);

        $settings = $this->settingService->update($request->validated());

        return ApiResponse::success(new CenterSettingResource($settings), __('center_settings.updated'));
    }
}
