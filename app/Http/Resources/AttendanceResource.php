<?php

namespace App\Http\Resources;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Attendance
 */
class AttendanceResource extends JsonResource
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
            'training_session_id' => $this->training_session_id,
            'status' => $this->status->value,
            'created_at' => $this->created_at,
        ];
    }
}
