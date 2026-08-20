<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Attendance\StoreAttendanceRequest;
use App\Http\Requests\Attendance\UpdateAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Responses\ApiResponse;
use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService) {}

    public function index(TrainingSession $session): JsonResponse
    {
        Gate::authorize('view', $session->project);

        return ApiResponse::success(
            AttendanceResource::collection($this->attendanceService->list($session)),
            __('attendance.listed'),
        );
    }

    public function store(StoreAttendanceRequest $request, TrainingSession $session): JsonResponse
    {
        Gate::authorize('update', $session->project);

        $attendance = $this->attendanceService->create($session, $request->validated());

        return ApiResponse::success(new AttendanceResource($attendance->load('trainee.user')), __('attendance.created'), 201);
    }

    public function show(Attendance $attendance): JsonResponse
    {
        Gate::authorize('view', $attendance->trainingSession->project);

        return ApiResponse::success(new AttendanceResource($attendance->load('trainee.user')), __('attendance.retrieved'));
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance): JsonResponse
    {
        Gate::authorize('update', $attendance->trainingSession->project);

        $attendance = $this->attendanceService->update($attendance, $request->validated());

        return ApiResponse::success(new AttendanceResource($attendance->load('trainee.user')), __('attendance.updated'));
    }

    public function destroy(Attendance $attendance): JsonResponse
    {
        Gate::authorize('update', $attendance->trainingSession->project);

        $this->attendanceService->delete($attendance);

        return ApiResponse::success(null, __('attendance.deleted'));
    }
}
