<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\TrainingSession;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user, TrainingSession $session): bool
    {
        return $this->owns($user, $session) || $user->can('attendance.view');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $this->owns($user, $attendance->trainingSession) || $user->can('attendance.view');
    }

    public function create(User $user, TrainingSession $session): bool
    {
        return $this->owns($user, $session) || $user->can('attendance.create');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $this->owns($user, $attendance->trainingSession) || $user->can('attendance.update');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $this->owns($user, $attendance->trainingSession) || $user->can('attendance.delete');
    }

    private function owns(User $user, TrainingSession $session): bool
    {
        return $user->trainer?->id === $session->project->trainer_id;
    }
}
