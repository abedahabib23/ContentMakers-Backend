<?php

namespace App\Http\Resources;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Storage paths live on the `private` disk and are never exposed directly —
 * only whether a document has been uploaded. Retrieving the file itself is
 * a separate, Policy-gated download endpoint.
 *
 * @mixin Trainer
 */
class TrainerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'bio' => $this->bio,
            'specialization' => $this->specialization,
            'years_of_experience' => $this->years_of_experience,
            'status' => $this->status->value,
            'has_id_photo' => $this->id_photo_path !== null,
            'has_cv' => $this->cv_path !== null,
            'certificates_count' => count($this->certificate_paths ?? []),
        ];
    }
}
