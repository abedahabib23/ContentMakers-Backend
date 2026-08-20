<?php

namespace App\Http\Requests\CenterSettings;

use App\Models\CenterSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCenterSettingRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', CenterSetting::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:30'],
            'email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'logo' => ['sometimes', 'nullable', 'file', 'mimes:svg,png,jpg,jpeg', 'max:2048'],
            'accepts_registrations' => ['sometimes', 'boolean'],
            'auto_issue_certificate' => ['sometimes', 'boolean'],
            'notify_email_on_new_request' => ['sometimes', 'boolean'],
        ];
    }
}
