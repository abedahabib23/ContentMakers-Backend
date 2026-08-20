<?php

namespace App\Policies;

use App\Models\Trainee;
use App\Models\TraineeOutput;
use App\Models\User;

class TraineeOutputPolicy
{
    public function viewAny(User $user, Trainee $trainee): bool
    {
        return $this->owns($user, $trainee) || $this->managesProject($user, $trainee) || $user->can('outputs.view');
    }

    public function view(User $user, TraineeOutput $output): bool
    {
        return $this->owns($user, $output->trainee) || $this->managesProject($user, $output->trainee) || $user->can('outputs.view');
    }

    /**
     * A trainee may submit outputs for their own enrollment; the
     * project's trainer may also add one on their behalf.
     */
    public function create(User $user, Trainee $trainee): bool
    {
        return $this->owns($user, $trainee) || $this->managesProject($user, $trainee) || $user->can('outputs.create');
    }

    public function update(User $user, TraineeOutput $output): bool
    {
        return $this->owns($user, $output->trainee) || $this->managesProject($user, $output->trainee) || $user->can('outputs.update');
    }

    /**
     * Reviewing (status + score) is a project-management action — the
     * trainee who submitted the work isn't the one who grades it.
     */
    public function review(User $user, TraineeOutput $output): bool
    {
        return $this->managesProject($user, $output->trainee) || $user->can('outputs.review');
    }

    public function delete(User $user, TraineeOutput $output): bool
    {
        return $this->owns($user, $output->trainee) || $this->managesProject($user, $output->trainee) || $user->can('outputs.delete');
    }

    private function owns(User $user, Trainee $trainee): bool
    {
        return $user->id === $trainee->user_id;
    }

    private function managesProject(User $user, Trainee $trainee): bool
    {
        return $user->trainer?->id === $trainee->project->trainer_id;
    }
}
