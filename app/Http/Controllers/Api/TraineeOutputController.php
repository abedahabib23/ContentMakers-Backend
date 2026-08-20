<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Outputs\ReviewTraineeOutputRequest;
use App\Http\Requests\Outputs\StoreTraineeOutputRequest;
use App\Http\Requests\Outputs\UpdateTraineeOutputRequest;
use App\Http\Resources\TraineeOutputResource;
use App\Http\Responses\ApiResponse;
use App\Models\Trainee;
use App\Models\TraineeOutput;
use App\Services\Outputs\TraineeOutputService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TraineeOutputController extends Controller
{
    public function __construct(private readonly TraineeOutputService $outputService) {}

    public function index(Trainee $trainee): JsonResponse
    {
        Gate::authorize('viewAny', [TraineeOutput::class, $trainee]);

        return ApiResponse::success(TraineeOutputResource::collection($this->outputService->list($trainee)), __('outputs.listed'));
    }

    public function store(StoreTraineeOutputRequest $request, Trainee $trainee): JsonResponse
    {
        Gate::authorize('create', [TraineeOutput::class, $trainee]);

        $output = $this->outputService->create($trainee, $request->validated());

        return ApiResponse::success(new TraineeOutputResource($output->load('trainee.user')), __('outputs.created'), 201);
    }

    public function show(TraineeOutput $output): JsonResponse
    {
        Gate::authorize('view', $output);

        return ApiResponse::success(new TraineeOutputResource($output->load('trainee.user')), __('outputs.retrieved'));
    }

    public function update(UpdateTraineeOutputRequest $request, TraineeOutput $output): JsonResponse
    {
        Gate::authorize('update', $output);

        $output = $this->outputService->update($output, $request->validated());

        return ApiResponse::success(new TraineeOutputResource($output->load('trainee.user')), __('outputs.updated'));
    }

    public function review(ReviewTraineeOutputRequest $request, TraineeOutput $output): JsonResponse
    {
        Gate::authorize('review', $output);

        $output = $this->outputService->review($output, $request->validated());

        return ApiResponse::success(new TraineeOutputResource($output->load('trainee.user')), __('outputs.reviewed'));
    }

    public function destroy(TraineeOutput $output): JsonResponse
    {
        Gate::authorize('delete', $output);

        $this->outputService->delete($output);

        return ApiResponse::success(null, __('outputs.deleted'));
    }
}
