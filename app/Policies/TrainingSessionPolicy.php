<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\TrainingSession;
use App\Models\User;

class TrainingSessionPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('sessions.view');
    }

    public function view(User $user, TrainingSession $session): bool
    {
        return $this->owns($user, $session->project) || $user->can('sessions.view');
    }

    public function create(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('sessions.create');
    }

    public function update(User $user, TrainingSession $session): bool
    {
        return $this->owns($user, $session->project) || $user->can('sessions.update');
    }

    public function delete(User $user, TrainingSession $session): bool
    {
        return $this->owns($user, $session->project) || $user->can('sessions.delete');
    }

    private function owns(User $user, Project $project): bool
    {
        return $user->trainer?->id === $project->trainer_id;
    }
}
