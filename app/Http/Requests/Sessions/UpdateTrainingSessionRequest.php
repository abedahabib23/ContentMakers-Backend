<?php

namespace App\Http\Requests\Sessions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateTrainingSessionRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see
     * StoreTrainingSessionRequest for why.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('session'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        // A session's project never changes after creation — only its
        // own fields are editable here.
        $session = $this->route('session');

        return [
            'trainer_id' => ['sometimes', 'integer', 'exists:trainers,id'],
            'number' => [
                'sometimes', 'required', 'integer', 'min:1',
                Rule::unique('training_sessions', 'number')
                    ->where(fn ($query) => $query->where('project_id', $session->project_id))
                    ->ignore($session),
            ],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'starts_at' => ['sometimes', 'required', 'date'],
            'ends_at' => ['sometimes', 'required', 'date', 'after:starts_at'],
            'hall' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
