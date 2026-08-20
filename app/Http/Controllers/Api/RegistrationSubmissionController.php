<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RegistrationSubmission;
use App\Services\Registration\RegistrationSubmissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationSubmissionController extends Controller
{
    public function idPhoto(RegistrationSubmission $submission): StreamedResponse
    {
        Gate::authorize('view', $submission->registrationForm->project);

        return Storage::disk(RegistrationSubmissionService::DISK)->download($submission->id_photo_path);
    }

    public function cv(RegistrationSubmission $submission): StreamedResponse
    {
        Gate::authorize('view', $submission->registrationForm->project);

        return Storage::disk(RegistrationSubmissionService::DISK)->download($submission->cv_path);
    }
}
