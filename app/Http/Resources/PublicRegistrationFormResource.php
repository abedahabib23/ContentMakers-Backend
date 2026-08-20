<?php

namespace App\Http\Resources;

use App\Models\RegistrationForm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * What an applicant sees before filling the form out — no internal ids,
 * no slug (they already have it, in the URL).
 *
 * @mixin RegistrationForm
 */
class PublicRegistrationFormResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'project_name' => $this->project->name,
            'seats_remaining' => max(0, $this->seats_count - $this->submissions()->count()),
            'deadline' => $this->deadline,
            'is_open' => $this->isOpen(),
        ];
    }
}
