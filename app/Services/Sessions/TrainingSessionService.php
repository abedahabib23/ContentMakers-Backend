<?php

namespace App\Services\Sessions;

use App\Models\Project;
use App\Models\TrainingSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TrainingSessionService
{
    /**
     * @return LengthAwarePaginator<int, TrainingSession>
     */
    public function list(Project $project): LengthAwarePaginator
    {
        return $project->trainingSessions()->with('trainer.user')->orderBy('number')->paginate();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(Project $project, array $data): TrainingSession
    {
        return $project->trainingSessions()->create([
            ...$data,
            // Defaults to the project's own trainer — a different one can
            // still be assigned explicitly (a substitute leading this
            // particular session).
            'trainer_id' => $data['trainer_id'] ?? $project->trainer_id,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TrainingSession $session, array $data): TrainingSession
    {
        $session->update($data);

        return $session;
    }

    public function delete(TrainingSession $session): void
    {
        $session->delete();
    }
}
