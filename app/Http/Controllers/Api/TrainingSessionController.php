<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sessions\StoreTrainingSessionRequest;
use App\Http\Requests\Sessions\UpdateTrainingSessionRequest;
use App\Http\Resources\TrainingSessionResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use App\Models\TrainingSession;
use App\Services\Sessions\TrainingSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TrainingSessionController extends Controller
{
    public function __construct(private readonly TrainingSessionService $sessionService) {}

    public function index(Project $project): JsonResponse
    {
        Gate::authorize('viewAny', [TrainingSession::class, $project]);

        $sessions = $this->sessionService->list($project);

        return ApiResponse::success(
            TrainingSessionResource::collection($sessions),
            __('sessions.listed'),
            200,
            [
                'current_page' => $sessions->currentPage(),
                'last_page' => $sessions->lastPage(),
                'per_page' => $sessions->perPage(),
                'total' => $sessions->total(),
            ],
        );
    }

    public function store(StoreTrainingSessionRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('create', [TrainingSession::class, $project]);

        $session = $this->sessionService->create($project, $request->validated());

        return ApiResponse::success(new TrainingSessionResource($session->load('trainer.user')), __('sessions.created'), 201);
    }

    public function show(TrainingSession $session): JsonResponse
    {
        Gate::authorize('view', $session);

        return ApiResponse::success(new TrainingSessionResource($session->load('trainer.user')), __('sessions.retrieved'));
    }

    public function update(UpdateTrainingSessionRequest $request, TrainingSession $session): JsonResponse
    {
        Gate::authorize('update', $session);

        $session = $this->sessionService->update($session, $request->validated());

        return ApiResponse::success(new TrainingSessionResource($session->load('trainer.user')), __('sessions.updated'));
    }

    public function destroy(TrainingSession $session): JsonResponse
    {
        Gate::authorize('delete', $session);

        $this->sessionService->delete($session);

        return ApiResponse::success(null, __('sessions.deleted'));
    }
}
