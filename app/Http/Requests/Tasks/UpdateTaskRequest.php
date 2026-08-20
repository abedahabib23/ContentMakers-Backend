<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see StoreTaskRequest for
     * why.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('task')->project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'max_score' => ['sometimes', 'required', 'integer', 'min:1'],
        ];
    }
}
