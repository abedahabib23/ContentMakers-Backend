<?php

namespace App\Models;

use App\Enums\OutputStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// score and status are deliberately absent — grading/review is a project
// manager's action, not something the submitting trainee can mass-assign
// on their own content update. See TraineeOutputService::review().
#[Fillable([
    'trainee_id',
    'project_id',
    'type',
    'work_url',
    'work_file_path',
    'work_description',
    'output_date',
])]
class TraineeOutput extends Model
{
    /**
     * Mirrors the column's DB-level default — see Trainer::$attributes for
     * why this is needed (Eloquent doesn't re-fetch server-side defaults
     * after an insert).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => OutputStatus::Draft->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'output_date' => 'date',
            'status' => OutputStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Trainee, $this>
     */
    public function trainee(): BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function isPublished(): bool
    {
        return $this->status === OutputStatus::Published;
    }
}
