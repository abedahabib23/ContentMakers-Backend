<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['project_id', 'slug', 'seats_count', 'deadline'])]
class RegistrationForm extends Model
{
    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Project, $this>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @return HasMany<RegistrationSubmission, $this>
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(RegistrationSubmission::class);
    }

    public function isOpen(): bool
    {
        // The center-wide "accepts_registrations" toggle closes every
        // registration form immediately, regardless of its own deadline
        // or remaining seats.
        return CenterSetting::current()->accepts_registrations
            && now()->lessThanOrEqualTo($this->deadline)
            && $this->submissions()->count() < $this->seats_count;
    }
}
