<?php

namespace App\Http\Resources;

use App\Models\TraineeOutput;
use App\Services\Outputs\TraineeOutputService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * @mixin TraineeOutput
 */
class TraineeOutputResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'trainee' => [
                'id' => $this->trainee->id,
                'name' => $this->trainee->user->name,
            ],
            'project_id' => $this->project_id,
            'type' => $this->type,
            'work_url' => $this->work_url,
            'work_file_url' => $this->work_file_path
                ? Storage::disk(TraineeOutputService::DISK)->url($this->work_file_path)
                : null,
            'work_description' => $this->work_description,
            'output_date' => $this->output_date,
            'score' => $this->score,
            'status' => $this->status->value,
            'created_at' => $this->created_at,
        ];
    }
}
