<?php

namespace App\Http\Requests\Attendance;

use App\Enums\AttendanceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class UpdateAttendanceRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see
     * StoreAttendanceRequest for why.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('attendance')->trainingSession->project);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(AttendanceStatus::class)],
        ];
    }
}
