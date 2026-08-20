<?php

namespace App\Services\Tasks;

use App\Models\CenterSetting;
use App\Models\Certificate;
use App\Models\Task;
use App\Models\TaskEvaluation;
use App\Models\Trainee;
use App\Services\Certificates\CertificateService;
use Illuminate\Database\Eloquent\Collection;

class TaskEvaluationService
{
    public function __construct(private readonly CertificateService $certificateService) {}

    /**
     * @return Collection<int, TaskEvaluation>
     */
    public function list(Task $task): Collection
    {
        return $task->evaluations()->with(['trainee.user', 'task'])->get();
    }

    /**
     * Creating an evaluation is the act of grading — a trainee's score is
     * set at creation, not assigned separately and graded later.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(Task $task, array $data): TaskEvaluation
    {
        $evaluation = $task->evaluations()->create([
            ...$data,
            'evaluated_at' => now(),
        ]);

        $this->maybeIssueCertificate($evaluation->trainee, $task);

        return $evaluation;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(TaskEvaluation $evaluation, array $data): TaskEvaluation
    {
        if (array_key_exists('score', $data)) {
            $data['evaluated_at'] = now();
        }

        $evaluation->update($data);

        $this->maybeIssueCertificate($evaluation->trainee, $evaluation->task);

        return $evaluation;
    }

    public function delete(TaskEvaluation $evaluation): void
    {
        $evaluation->delete();
    }

    /**
     * A trainee "completes" a project once they've been evaluated on
     * every one of its tasks. Only fires when the center-wide toggle is
     * on, there's at least one task to complete, and no certificate has
     * been issued for this trainee+project yet.
     */
    private function maybeIssueCertificate(Trainee $trainee, Task $task): void
    {
        if (! CenterSetting::current()->auto_issue_certificate) {
            return;
        }

        $project = $task->project;

        $alreadyIssued = Certificate::where('trainee_id', $trainee->id)
            ->where('project_id', $project->id)
            ->exists();

        if ($alreadyIssued) {
            return;
        }

        $taskIds = $project->tasks()->pluck('id');

        if ($taskIds->isEmpty()) {
            return;
        }

        $evaluatedTaskCount = TaskEvaluation::where('trainee_id', $trainee->id)
            ->whereIn('task_id', $taskIds)
            ->count();

        if ($evaluatedTaskCount < $taskIds->count()) {
            return;
        }

        $this->certificateService->create($trainee, []);
    }
}
