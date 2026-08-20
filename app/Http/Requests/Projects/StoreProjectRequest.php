<?php

namespace App\Http\Requests\Projects;

use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreProjectRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', Project::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $canManageAny = $this->user()->can('projects.create');

        return [
            'name' => ['required', 'string', 'max:255'],
            'number' => ['required', 'string', 'max:255', 'unique:projects,number'],
            'image' => ['sometimes', 'file', 'image', 'max:5120'],
            'sessions_count' => ['sometimes', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            // A self-service trainer creates only for themselves — the
            // controller fills trainer_id from their own profile. Only
            // someone with projects.create may assign a project to a
            // different trainer.
            'trainer_id' => $canManageAny
                ? ['sometimes', 'integer', 'exists:trainers,id']
                : ['prohibited'],
        ];
    }
}
