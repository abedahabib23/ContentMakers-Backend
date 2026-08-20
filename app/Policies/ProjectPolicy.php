<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->trainer !== null || $user->can('projects.view');
    }

    public function view(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('projects.view');
    }

    /**
     * Any trainer may create projects for themselves; anyone else needs
     * projects.create.
     */
    public function create(User $user): bool
    {
        return $user->trainer !== null || $user->can('projects.create');
    }

    public function update(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('projects.update');
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('projects.delete');
    }

    private function owns(User $user, Project $project): bool
    {
        return $user->trainer?->id === $project->trainer_id;
    }
}
