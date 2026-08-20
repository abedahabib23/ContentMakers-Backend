<?php

namespace App\Services\Tasks;

use App\Models\Task;
use App\Models\TaskEvaluation;
use Illuminate\Database\Eloquent\Collection;

class TaskEvaluationService
{
    /**
     * @return Collection<int, TaskEvaluation>
     */
    public function list(Task $task): Collection
    {
        return $task->evaluations()->with(['trainee.user', 'task'])->get();
    }

    /**
     * Creating an evaluation is the act of grading — a trainee's score is
     * set at creation, not assigned separately and graded later.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(Task $task, array $data): TaskEvaluation
    {
        return $task->evaluations()->create([
            ...$data,
            'evaluated_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TaskEvaluation $evaluation, array $data): TaskEvaluation
    {
        if (array_key_exists('score', $data)) {
            $data['evaluated_at'] = now();
        }

        $evaluation->update($data);

        return $evaluation;
    }

    public function delete(TaskEvaluation $evaluation): void
    {
        $evaluation->delete();
    }
}
