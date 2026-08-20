<?php

namespace App\Http\Requests\Users;

use App\Enums\UserType;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', User::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', Password::default()],
            'type' => ['required', new Enum(UserType::class)],
        ];
    }
}
