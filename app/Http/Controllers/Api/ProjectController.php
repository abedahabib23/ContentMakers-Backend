<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Projects\StoreProjectRequest;
use App\Http\Requests\Projects\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use App\Services\Projects\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectController extends Controller
{
    public function __construct(private readonly ProjectService $projectService) {}

    public function index(Request $request): JsonResponse
    {
        Gate::authorize('viewAny', Project::class);

        $projects = $this->projectService->list($request->user());

        return ApiResponse::success(
            ProjectResource::collection($projects),
            __('projects.listed'),
            200,
            [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        );
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        Gate::authorize('create', Project::class);

        $trainerId = $request->validated('trainer_id') ?? $request->user()->trainer?->id;

        abort_if($trainerId === null, 422, __('projects.trainer_required'));

        $project = $this->projectService->create([
            ...$request->validated(),
            'trainer_id' => $trainerId,
        ]);

        return ApiResponse::success(new ProjectResource($project->load('trainer.user')), __('projects.created'), 201);
    }

    public function show(Project $project): JsonResponse
    {
        Gate::authorize('view', $project);

        return ApiResponse::success(new ProjectResource($project->load('trainer.user')), __('projects.retrieved'));
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('update', $project);

        $project = $this->projectService->update($project, $request->validated());

        return ApiResponse::success(new ProjectResource($project->load('trainer.user')), __('projects.updated'));
    }

    public function destroy(Project $project): JsonResponse
    {
        Gate::authorize('delete', $project);

        $this->projectService->delete($project);

        return ApiResponse::success(null, __('projects.deleted'));
    }
}
