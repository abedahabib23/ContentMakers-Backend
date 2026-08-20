<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trainees\IndexTraineeRequest;
use App\Http\Resources\TraineeResource;
use App\Http\Responses\ApiResponse;
use App\Models\Trainee;
use App\Services\Trainees\TraineeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TraineeController extends Controller
{
    public function __construct(private readonly TraineeService $traineeService) {}

    public function index(IndexTraineeRequest $request): JsonResponse
    {
        $trainees = $this->traineeService->list($request->user(), $request->validated());

        return ApiResponse::success(
            TraineeResource::collection($trainees),
            __('trainees.listed'),
            200,
            [
                'current_page' => $trainees->currentPage(),
                'last_page' => $trainees->lastPage(),
                'per_page' => $trainees->perPage(),
                'total' => $trainees->total(),
            ],
        );
    }

    public function show(Trainee $trainee): JsonResponse
    {
        Gate::authorize('view', $trainee);

        return ApiResponse::success(new TraineeResource($this->traineeService->find($trainee)), __('trainees.retrieved'));
    }
}
