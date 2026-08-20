<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Trainers\UploadTrainerFilesRequest;
use App\Http\Resources\TrainerResource;
use App\Http\Responses\ApiResponse;
use App\Models\Trainer;
use App\Services\Trainers\TrainerFileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainerFileController extends Controller
{
    public function __construct(private readonly TrainerFileService $trainerFileService) {}

    public function update(UploadTrainerFilesRequest $request, Trainer $trainer): JsonResponse
    {
        Gate::authorize('update', $trainer);

        $trainer = $this->trainerFileService->upload($trainer, $request->validated());

        return ApiResponse::success(new TrainerResource($trainer), __('trainers.files_uploaded'));
    }

    public function idPhoto(Trainer $trainer): StreamedResponse
    {
        Gate::authorize('view', $trainer);

        abort_if($trainer->id_photo_path === null, 404);

        return Storage::disk(TrainerFileService::DISK)->download($trainer->id_photo_path);
    }

    public function cv(Trainer $trainer): StreamedResponse
    {
        Gate::authorize('view', $trainer);

        abort_if($trainer->cv_path === null, 404);

        return Storage::disk(TrainerFileService::DISK)->download($trainer->cv_path);
    }

    public function certificate(Trainer $trainer, int $index): StreamedResponse
    {
        Gate::authorize('view', $trainer);

        $path = $trainer->certificate_paths[$index] ?? null;

        abort_if($path === null, 404);

        return Storage::disk(TrainerFileService::DISK)->download($path);
    }
}
