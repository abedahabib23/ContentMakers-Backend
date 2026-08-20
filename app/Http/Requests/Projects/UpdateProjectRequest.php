<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $canManageAny = $this->user()->can('projects.update');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('projects', 'number')->ignore($this->route('project'))],
            'image' => ['sometimes', 'file', 'image', 'max:5120'],
            'sessions_count' => ['sometimes', 'integer', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
            // Reassigning a project to a different trainer is an admin-level
            // action, not something the owning trainer can do to themselves.
            'trainer_id' => $canManageAny
                ? ['sometimes', 'integer', 'exists:trainers,id']
                : ['prohibited'],
        ];
    }
}
