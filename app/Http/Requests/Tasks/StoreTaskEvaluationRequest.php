<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreTaskEvaluationRequest extends FormRequest
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
        $task = $this->route('task');

        return [
            'trainee_id' => [
                'required', 'integer',
                // Must be enrolled in the same project this task belongs
                // to — not just any trainee in the system.
                Rule::exists('trainees', 'id')->where(fn ($query) => $query->where('project_id', $task->project_id)),
                Rule::unique('task_evaluations', 'trainee_id')->where(fn ($query) => $query->where('task_id', $task->id)),
            ],
            'score' => ['required', 'integer', 'min:0', 'max:'.$task->max_score],
            'feedback' => ['nullable', 'string'],
        ];
    }
}
