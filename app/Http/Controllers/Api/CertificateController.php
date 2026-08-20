<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Certificates\StoreCertificateRequest;
use App\Http\Requests\Certificates\UpdateCertificateRequest;
use App\Http\Resources\CertificateResource;
use App\Http\Responses\ApiResponse;
use App\Models\Certificate;
use App\Models\Trainee;
use App\Services\Certificates\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CertificateController extends Controller
{
    public function __construct(private readonly CertificateService $certificateService) {}

    public function index(Trainee $trainee): JsonResponse
    {
        Gate::authorize('viewAny', [Certificate::class, $trainee]);

        return ApiResponse::success(CertificateResource::collection($this->certificateService->list($trainee)), __('certificates.listed'));
    }

    public function store(StoreCertificateRequest $request, Trainee $trainee): JsonResponse
    {
        Gate::authorize('create', [Certificate::class, $trainee]);

        $certificate = $this->certificateService->create($trainee, $request->validated());

        return ApiResponse::success(new CertificateResource($certificate->load('trainee.user')), __('certificates.created'), 201);
    }

    public function show(Certificate $certificate): JsonResponse
    {
        Gate::authorize('view', $certificate);

        return ApiResponse::success(new CertificateResource($certificate->load('trainee.user')), __('certificates.retrieved'));
    }

    public function update(UpdateCertificateRequest $request, Certificate $certificate): JsonResponse
    {
        Gate::authorize('update', $certificate);

        $certificate = $this->certificateService->update($certificate, $request->validated());

        return ApiResponse::success(new CertificateResource($certificate->load('trainee.user')), __('certificates.updated'));
    }

    public function destroy(Certificate $certificate): JsonResponse
    {
        Gate::authorize('delete', $certificate);

        $this->certificateService->delete($certificate);

        return ApiResponse::success(null, __('certificates.deleted'));
    }
}
