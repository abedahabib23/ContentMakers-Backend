<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTaskEvaluationRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see StoreTaskRequest for
     * why.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('evaluation')->task->project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxScore = $this->route('evaluation')->task->max_score;

        return [
            'score' => ['sometimes', 'required', 'integer', 'min:0', "max:{$maxScore}"],
            'feedback' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
