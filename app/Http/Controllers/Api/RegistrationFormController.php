<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Registration\StoreRegistrationFormRequest;
use App\Http\Resources\RegistrationFormResource;
use App\Http\Resources\RegistrationSubmissionResource;
use App\Http\Responses\ApiResponse;
use App\Models\Project;
use App\Models\RegistrationForm;
use App\Services\Registration\RegistrationFormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class RegistrationFormController extends Controller
{
    public function __construct(private readonly RegistrationFormService $formService) {}

    public function index(Project $project): JsonResponse
    {
        Gate::authorize('viewAny', [RegistrationForm::class, $project]);

        return ApiResponse::success(
            RegistrationFormResource::collection($project->registrationForms()->latest()->get()),
            __('registration.forms_listed'),
        );
    }

    public function store(StoreRegistrationFormRequest $request, Project $project): JsonResponse
    {
        Gate::authorize('create', [RegistrationForm::class, $project]);

        $form = $this->formService->create($project, $request->validated());

        return ApiResponse::success(new RegistrationFormResource($form), __('registration.form_created'), 201);
    }

    public function submissions(RegistrationForm $form): JsonResponse
    {
        Gate::authorize('view', $form);

        $submissions = $form->submissions()->latest()->paginate();

        return ApiResponse::success(
            RegistrationSubmissionResource::collection($submissions),
            __('registration.submissions_listed'),
            200,
            [
                'current_page' => $submissions->currentPage(),
                'last_page' => $submissions->lastPage(),
                'per_page' => $submissions->perPage(),
                'total' => $submissions->total(),
            ],
        );
    }
}
