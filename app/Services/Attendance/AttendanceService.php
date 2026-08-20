<?php

namespace App\Services\Attendance;

use App\Models\Attendance;
use App\Models\TrainingSession;
use Illuminate\Database\Eloquent\Collection;

class AttendanceService
{
    /**
     * @return Collection<int, Attendance>
     */
    public function list(TrainingSession $session): Collection
    {
        return $session->attendances()->with('trainee.user')->get();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(TrainingSession $session, array $data): Attendance
    {
        return $session->attendances()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Attendance $attendance, array $data): Attendance
    {
        $attendance->update($data);

        return $attendance;
    }

    public function delete(Attendance $attendance): void
    {
        $attendance->delete();
    }
}
