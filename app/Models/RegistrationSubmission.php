<?php

namespace App\Models;

use App\Enums\ApplicantLevel;
use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_form_id',
    'full_name',
    'email',
    'phone',
    'city',
    'interests',
    'current_skills',
    'level',
    'id_photo_path',
    'cv_path',
    'portfolio_url',
    'motivation',
])]
class RegistrationSubmission extends Model
{
    /**
     * Mirrors the column's DB-level default — see Trainer::$attributes for
     * why this is needed (Eloquent doesn't re-fetch server-side defaults
     * after an insert).
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => SubmissionStatus::Pending->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'level' => ApplicantLevel::class,
            'status' => SubmissionStatus::class,
        ];
    }

    /**
     * @return BelongsTo<RegistrationForm, $this>
     */
    public function registrationForm(): BelongsTo
    {
        return $this->belongsTo(RegistrationForm::class);
    }
}
