<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['task_id', 'trainee_id', 'score', 'feedback', 'evaluated_at'])]
class TaskEvaluation extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'evaluated_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Task, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    /**
     * @return BelongsTo<Trainee, $this>
     */
    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function isGraded(): bool
    {
        return $this->score !== null;
    }
}
