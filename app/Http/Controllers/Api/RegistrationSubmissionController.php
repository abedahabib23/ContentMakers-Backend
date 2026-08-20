<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegistrationSubmissionResource;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Models\RegistrationSubmission;
use App\Services\Registration\RegistrationSubmissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationSubmissionController extends Controller
{
    public function __construct(private readonly RegistrationSubmissionService $submissionService) {}

    public function idPhoto(RegistrationSubmission $submission): StreamedResponse
    {
        Gate::authorize('view', $submission->registrationForm);

        return Storage::disk(RegistrationSubmissionService::DISK)->download($submission->id_photo_path);
    }

    public function cv(RegistrationSubmission $submission): StreamedResponse
    {
        Gate::authorize('view', $submission->registrationForm);

        return Storage::disk(RegistrationSubmissionService::DISK)->download($submission->cv_path);
    }

    public function accept(RegistrationSubmission $submission): JsonResponse
    {
        Gate::authorize('review', $submission->registrationForm);

        $result = $this->submissionService->accept($submission);

        // The password is returned once, here, and never stored or logged
        // in plaintext anywhere else — hand it to the applicant now.
        return ApiResponse::success([
            'user' => new UserResource($result['user']),
            'temporary_password' => $result['password'],
        ], __('registration.accepted'));
    }

    public function reject(RegistrationSubmission $submission): JsonResponse
    {
        Gate::authorize('review', $submission->registrationForm);

        $submission = $this->submissionService->reject($submission);

        return ApiResponse::success(new RegistrationSubmissionResource($submission), __('registration.rejected'));
    }
}
