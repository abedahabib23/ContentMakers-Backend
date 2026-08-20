<?php

namespace App\Http\Resources;

use App\Models\TaskEvaluation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TaskEvaluation
 */
class TaskEvaluationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'task_id' => $this->task_id,
            'trainee' => [
                'id' => $this->trainee->id,
                'name' => $this->trainee->user->name,
            ],
            'score' => $this->score,
            'max_score' => $this->task->max_score,
            'feedback' => $this->feedback,
            'is_graded' => $this->isGraded(),
            'evaluated_at' => $this->evaluated_at,
        ];
    }
}
