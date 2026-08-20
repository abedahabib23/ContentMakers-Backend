<?php

namespace App\Http\Resources;

use App\Models\RegistrationSubmission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Document paths live on the `private` disk and are never exposed
 * directly — only whether they were uploaded. Matches TrainerResource's
 * convention for the same kind of sensitive attachment.
 *
 * @mixin RegistrationSubmission
 */
class RegistrationSubmissionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'interests' => $this->interests,
            'current_skills' => $this->current_skills,
            'level' => $this->level->value,
            'has_id_photo' => $this->id_photo_path !== null,
            'has_cv' => $this->cv_path !== null,
            'portfolio_url' => $this->portfolio_url,
            'motivation' => $this->motivation,
            'created_at' => $this->created_at,
        ];
    }
}
