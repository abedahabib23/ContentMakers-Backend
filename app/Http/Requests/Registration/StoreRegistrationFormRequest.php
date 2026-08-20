<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRegistrationFormRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed. Ability is "update" on the parent project.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('project'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'seats_count' => ['required', 'integer', 'min:1'],
            'deadline' => ['required', 'date', 'after:now'],
        ];
    }
}
