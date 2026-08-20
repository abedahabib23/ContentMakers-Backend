<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\TaskEvaluation;
use App\Models\User;

class TaskEvaluationPolicy
{
    public function viewAny(User $user, Task $task): bool
    {
        return $this->owns($user, $task) || $user->can('task_evaluations.view');
    }

    public function view(User $user, TaskEvaluation $evaluation): bool
    {
        return $this->owns($user, $evaluation->task) || $user->can('task_evaluations.view');
    }

    public function create(User $user, Task $task): bool
    {
        return $this->owns($user, $task) || $user->can('task_evaluations.create');
    }

    public function update(User $user, TaskEvaluation $evaluation): bool
    {
        return $this->owns($user, $evaluation->task) || $user->can('task_evaluations.update');
    }

    public function delete(User $user, TaskEvaluation $evaluation): bool
    {
        return $this->owns($user, $evaluation->task) || $user->can('task_evaluations.delete');
    }

    private function owns(User $user, Task $task): bool
    {
        return $user->trainer?->id === $task->project->trainer_id;
    }
}
