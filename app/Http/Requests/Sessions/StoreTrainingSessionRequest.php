<?php

namespace App\Http\Requests\Sessions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTrainingSessionRequest extends FormRequest
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
        // project_id comes from the route, not the request body — a
        // session can only ever be created under the project it's nested
        // under (see routes/sessions.php), so there's nothing to validate
        // or spoof here.
        $projectId = $this->route('project')->id;

        return [
            'trainer_id' => ['sometimes', 'integer', 'exists:trainers,id'],
            'number' => [
                'required', 'integer', 'min:1',
                Rule::unique('training_sessions', 'number')->where(fn ($query) => $query->where('project_id', $projectId)),
            ],
            'title' => ['required', 'string', 'max:255'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'hall' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
