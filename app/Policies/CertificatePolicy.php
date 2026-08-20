<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\Trainee;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user, Trainee $trainee): bool
    {
        return $this->owns($user, $trainee) || $this->managesProject($user, $trainee) || $user->can('certificates.view');
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return $this->owns($user, $certificate->trainee) || $this->managesProject($user, $certificate->trainee) || $user->can('certificates.view');
    }

    /**
     * Issuing a certificate is a project-management action — a trainee
     * doesn't issue their own.
     */
    public function create(User $user, Trainee $trainee): bool
    {
        return $this->managesProject($user, $trainee) || $user->can('certificates.create');
    }

    public function update(User $user, Certificate $certificate): bool
    {
        return $this->managesProject($user, $certificate->trainee) || $user->can('certificates.update');
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $this->managesProject($user, $certificate->trainee) || $user->can('certificates.delete');
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
