<?php

namespace App\Services\Trainees;

use App\Models\Trainee;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TraineeService
{
    /**
     * @param  array{search?: string|null, project_id?: int|null, level?: string|null}  $filters
     * @return LengthAwarePaginator<int, Trainee>
     */
    public function list(User $actor, array $filters = []): LengthAwarePaginator
    {
        $query = Trainee::with(['user', 'project', 'registrationSubmission'])->latest();

        // Without trainees.view, a trainer only sees trainees enrolled in
        // their own projects — viewAny grants access to the endpoint, not
        // to every trainer's roster.
        if (! $actor->can('trainees.view')) {
            $query->whereHas('project', fn ($q) => $q->where('trainer_id', $actor->trainer?->id));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];

            // ilike, not like — Postgres LIKE is case-sensitive.
            $query->whereHas('user', fn ($q) => $q->where('name', 'ilike', "%{$search}%")
                ->orWhere('email', 'ilike', "%{$search}%"));
        }

        if (! empty($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        if (! empty($filters['level'])) {
            $query->whereHas('registrationSubmission', fn ($q) => $q->where('level', $filters['level']));
        }

        return $query->paginate();
    }

    public function find(Trainee $trainee): Trainee
    {
        return $trainee->load(['user', 'project', 'registrationSubmission']);
    }
}
