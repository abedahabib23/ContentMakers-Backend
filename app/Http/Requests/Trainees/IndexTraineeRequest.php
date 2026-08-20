<?php

namespace App\Http\Requests\Trainees;

use App\Enums\ApplicantLevel;
use App\Models\Trainee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class IndexTraineeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Trainee::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'project_id' => ['sometimes', 'nullable', 'integer', 'exists:projects,id'],
            'level' => ['sometimes', 'nullable', new Enum(ApplicantLevel::class)],
        ];
    }
}
