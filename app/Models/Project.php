<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'number',
    'image_path',
    'trainer_id',
    'sessions_count',
    'description',
])]
class Project extends Model
{
    /**
     * @return BelongsTo<Trainer, $this>
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }

    /**
     * @return HasMany<TrainingSession, $this>
     */
    public function trainingSessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    /**
     * @return HasMany<RegistrationForm, $this>
     */
    public function registrationForms(): HasMany
    {
        return $this->hasMany(RegistrationForm::class);
    }

    /**
     * @return HasMany<Trainee, $this>
     */
    public function trainees(): HasMany
    {
        return $this->hasMany(Trainee::class);
    }

    /**
     * @return HasMany<Task, $this>
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
