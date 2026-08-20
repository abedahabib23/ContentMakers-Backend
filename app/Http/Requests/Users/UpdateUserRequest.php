<?php

namespace App\Http\Requests\Users;

use App\Enums\UserType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => ['sometimes', Password::default()],
            // Self-editing users reach this request too (UserPolicy::update
            // allows self-edit without users.update) — only someone with
            // users.update may change a type, otherwise it's a privilege
            // escalation route (a trainee promoting themselves to super_admin).
            'type' => $this->user()->can('users.update')
                ? ['sometimes', new Enum(UserType::class)]
                : ['prohibited'],
        ];
    }
}
