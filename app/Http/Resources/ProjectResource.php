<?php

namespace App\Http\Resources;

use App\Models\Project;
use App\Services\Projects\ProjectService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin Project
 */
class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'number' => $this->number,
            'image_url' => $this->image_path
                ? Storage::disk(ProjectService::IMAGE_DISK)->url($this->image_path)
                : null,
            'trainer' => [
                'id' => $this->trainer->id,
                'name' => $this->trainer->user->name,
            ],
            'sessions_count' => $this->sessions_count,
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
