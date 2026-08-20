<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('tasks.view');
    }

    public function view(User $user, Task $task): bool
    {
        return $this->owns($user, $task->project) || $user->can('tasks.view');
    }

    public function create(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('tasks.create');
    }

    public function update(User $user, Task $task): bool
    {
        return $this->owns($user, $task->project) || $user->can('tasks.update');
    }

    public function delete(User $user, Task $task): bool
    {
        return $this->owns($user, $task->project) || $user->can('tasks.delete');
    }

    private function owns(User $user, Project $project): bool
    {
        return $user->trainer?->id === $project->trainer_id;
    }
}
