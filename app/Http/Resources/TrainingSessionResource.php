<?php

namespace App\Http\Resources;

use App\Models\TrainingSession;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TrainingSession
 */
class TrainingSessionResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'project_id' => $this->project_id,
            'trainer' => [
                'id' => $this->trainer->id,
                'name' => $this->trainer->user->name,
            ],
            'number' => $this->number,
            'title' => $this->title,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'hall' => $this->hall,
            'description' => $this->description,
        ];
    }
}
