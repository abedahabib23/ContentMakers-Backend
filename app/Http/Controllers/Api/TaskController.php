<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tasks\StoreTaskRequest;
use App\Http\Requests\Tasks\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use App\Models\Task;
use App\Services\Tasks\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function __construct(private readonly TaskService $taskService) {}

    public function index(Project $project): JsonResponse
    {
        Gate::authorize('viewAny', [Task::class, $project]);

        return ApiResponse::success(TaskResource::collection($this->taskService->list($project)), __('tasks.listed'));
    }

    public function store(StoreTaskRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('create', [Task::class, $project]);

        $task = $this->taskService->create($project, $request->validated());

        return ApiResponse::success(new TaskResource($task), __('tasks.created'), 201);
    }

    public function show(Task $task): JsonResponse
    {
        Gate::authorize('view', $task);

        return ApiResponse::success(new TaskResource($task), __('tasks.retrieved'));
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        Gate::authorize('update', $task);

        $task = $this->taskService->update($task, $request->validated());

        return ApiResponse::success(new TaskResource($task), __('tasks.updated'));
    }

    public function destroy(Task $task): JsonResponse
    {
        Gate::authorize('delete', $task);

        $this->taskService->delete($task);

        return ApiResponse::success(null, __('tasks.deleted'));
    }
}
