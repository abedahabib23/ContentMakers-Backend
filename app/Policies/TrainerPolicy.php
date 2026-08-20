<?php

namespace App\Policies;

use App\Models\Trainer;
use App\Models\User;

class TrainerPolicy
{
    public function view(User $user, Trainer $trainer): bool
    {
        return $user->id === $trainer->user_id || $user->can('trainers.view');
    }

    /**
     * A trainer may always manage their own profile (including uploading
     * their own documents); anyone else needs trainers.update.
     */
    public function update(User $user, Trainer $trainer): bool
    {
        return $user->id === $trainer->user_id || $user->can('trainers.update');
    }
}
