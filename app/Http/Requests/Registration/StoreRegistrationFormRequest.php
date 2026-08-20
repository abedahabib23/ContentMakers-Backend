<?php

namespace App\Http\Requests\Registration;

use App\Models\RegistrationForm;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreRegistrationFormRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', [RegistrationForm::class, $this->route('project')]);
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
