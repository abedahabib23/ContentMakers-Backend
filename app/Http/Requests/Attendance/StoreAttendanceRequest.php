<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreAttendanceRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details (e.g. "already taken")
     * before ever being told they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('session')->project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $session = $this->route('session');

        return [
            'trainee_id' => [
                'required', 'integer',
                // Must be enrolled in the same project this session
                // belongs to — not just any trainee in the system.
                Rule::exists('trainees', 'id')->where(fn ($query) => $query->where('project_id', $session->project_id)),
                Rule::unique('attendances', 'trainee_id')->where(fn ($query) => $query->where('training_session_id', $session->id)),
            ],
            'status' => ['required', new Enum(AttendanceStatus::class)],
        ];
    }
}
