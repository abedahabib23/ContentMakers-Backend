<?php

namespace App\Http\Requests\Projects;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class IndexProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('viewAny', Project::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'trainer_id' => ['sometimes', 'nullable', 'integer', 'exists:trainers,id'],
        ];
    }
}
