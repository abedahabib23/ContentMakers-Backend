<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
