<?php

namespace App\Policies;

use App\Models\Trainee;
use App\Models\User;

class TraineePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->trainer !== null || $user->can('trainees.view');
    }

    public function view(User $user, Trainee $trainee): bool
    {
        return $user->id === $trainee->user_id
            || $user->trainer?->id === $trainee->project->trainer_id
            || $user->can('trainees.view');
    }
}
