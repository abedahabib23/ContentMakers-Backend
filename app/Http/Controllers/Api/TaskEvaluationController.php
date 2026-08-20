<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskEvaluationRequest;
use App\Http\Requests\Tasks\UpdateTaskEvaluationRequest;
use App\Http\Resources\TaskEvaluationResource;
use App\Http\Responses\ApiResponse;
use App\Models\Task;
use App\Models\TaskEvaluation;
use App\Services\Tasks\TaskEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TaskEvaluationController extends Controller
{
    public function __construct(private readonly TaskEvaluationService $evaluationService) {}

    public function index(Task $task): JsonResponse
    {
        Gate::authorize('viewAny', [TaskEvaluation::class, $task]);

        return ApiResponse::success(
            TaskEvaluationResource::collection($this->evaluationService->list($task)),
            __('tasks.evaluations_listed'),
        );
    }

    public function store(StoreTaskEvaluationRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('create', [TaskEvaluation::class, $task]);

        $evaluation = $this->evaluationService->create($task, $request->validated());

        return ApiResponse::success(
            new TaskEvaluationResource($evaluation->load('trainee.user', 'task')),
            __('tasks.evaluation_created'),
            201,
        );
    }

    public function show(TaskEvaluation $evaluation): JsonResponse
    {
        Gate::authorize('view', $evaluation);

        return ApiResponse::success(
            new TaskEvaluationResource($evaluation->load('trainee.user', 'task')),
            __('tasks.evaluation_retrieved'),
        );
    }

    public function update(UpdateTaskEvaluationRequest $request, TaskEvaluation $evaluation): JsonResponse
    {
        Gate::authorize('update', $evaluation);

        $evaluation = $this->evaluationService->update($evaluation, $request->validated());

        return ApiResponse::success(
            new TaskEvaluationResource($evaluation->load('trainee.user', 'task')),
            __('tasks.evaluation_updated'),
        );
    }

    public function destroy(TaskEvaluation $evaluation): JsonResponse
    {
        Gate::authorize('delete', $evaluation);

        $this->evaluationService->delete($evaluation);

        return ApiResponse::success(null, __('tasks.evaluation_deleted'));
    }
}
