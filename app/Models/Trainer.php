<?php

namespace App\Models;

use App\Enums\TrainerStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'bio',
    'specialization',
    'years_of_experience',
    'id_photo_path',
    'cv_path',
    'certificate_paths',
    'status',
])]
class Trainer extends Model
{
    /**
     * Mirrors the column's DB-level default — Eloquent doesn't re-fetch
     * server-side defaults after an insert, so without this a freshly
     * created instance's $status would read as null in the same request
     * even though the row itself has 'active'.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => TrainerStatus::Active->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'certificate_paths' => 'array',
            'status' => TrainerStatus::class,
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Project, $this>
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
