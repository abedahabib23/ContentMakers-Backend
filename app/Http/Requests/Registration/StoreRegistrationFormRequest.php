<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
