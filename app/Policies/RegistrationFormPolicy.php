<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\RegistrationForm;
use App\Models\User;

class RegistrationFormPolicy
{
    public function viewAny(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('registration_forms.view');
    }

    public function view(User $user, RegistrationForm $form): bool
    {
        return $this->owns($user, $form->project) || $user->can('registration_forms.view');
    }

    public function create(User $user, Project $project): bool
    {
        return $this->owns($user, $project) || $user->can('registration_forms.create');
    }

    /**
     * Accepting/rejecting an application is a decision on the form's
     * submissions — distinct from just viewing them.
     */
    public function review(User $user, RegistrationForm $form): bool
    {
        return $this->owns($user, $form->project) || $user->can('registration_forms.review');
    }

    private function owns(User $user, Project $project): bool
    {
        return $user->trainer?->id === $project->trainer_id;
    }
}
