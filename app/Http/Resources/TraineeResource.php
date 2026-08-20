<?php

namespace App\Http\Resources;

use App\Models\Trainee;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Trainee
 */
class TraineeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'project' => [
                'id' => $this->project->id,
                'name' => $this->project->name,
            ],
            'phone' => $this->registrationSubmission->phone,
            'city' => $this->registrationSubmission->city,
            'level' => $this->registrationSubmission->level->value,
            'created_at' => $this->created_at,
        ];
    }
}
