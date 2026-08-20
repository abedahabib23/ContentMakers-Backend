<?php

namespace App\Services\Tasks;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    /**
     * @return Collection<int, Task>
     */
    public function list(Project $project): Collection
    {
        return $project->tasks()->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Project $project, array $data): Task
    {
        return $project->tasks()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Task $task, array $data): Task
    {
        $task->update($data);

        return $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }
}
