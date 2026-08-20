<?php

namespace App\Http\Resources;

use App\Models\RegistrationForm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Admin-facing — includes the shareable link. See
 * PublicRegistrationFormResource for what applicants themselves see.
 *
 * @mixin RegistrationForm
 */
class RegistrationFormResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'seats_count' => $this->seats_count,
            'submissions_count' => $this->submissions()->count(),
            'deadline' => $this->deadline,
            'is_open' => $this->isOpen(),
            'registration_url' => rtrim(config('app.frontend_url'), '/').'/register/'.$this->slug,
            'created_at' => $this->created_at,
        ];
    }
}
