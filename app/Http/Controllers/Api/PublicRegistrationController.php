<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\SubmitRegistrationRequest;
use App\Http\Resources\PublicRegistrationFormResource;
use App\Http\Responses\ApiResponse;
use App\Models\RegistrationForm;
use App\Services\Registration\RegistrationSubmissionService;
use Illuminate\Http\JsonResponse;

/**
 * Unauthenticated — reached by candidates via the public registration
 * link, not by logged-in app users. Never gated by Gate::authorize().
 */
class PublicRegistrationController extends Controller
{
    public function __construct(private readonly RegistrationSubmissionService $submissionService) {}

    public function show(RegistrationForm $form): JsonResponse
    {
        return ApiResponse::success(new PublicRegistrationFormResource($form), __('registration.form_retrieved'));
    }

    public function store(SubmitRegistrationRequest $request, RegistrationForm $form): JsonResponse
    {
        $this->submissionService->submit($form, $request->validated());

        return ApiResponse::success(null, __('registration.submitted'), 201);
    }
}
