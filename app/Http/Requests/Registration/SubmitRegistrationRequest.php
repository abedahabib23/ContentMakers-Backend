<?php

namespace App\Http\Requests\Registration;

use App\Enums\ApplicantLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class SubmitRegistrationRequest extends FormRequest
{
    /**
     * Public endpoint — anyone may apply. Whether the form is still
     * accepting applications (deadline/seats) is checked separately by
     * RegistrationSubmissionService, not here.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $formId = $this->route('form')->id;

        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'string', 'email', 'max:255',
                Rule::unique('registration_submissions', 'email')->where(fn ($query) => $query->where('registration_form_id', $formId)),
            ],
            'phone' => ['required', 'string', 'max:30'],
            'city' => ['required', 'string', 'max:255'],
            'interests' => ['nullable', 'string', 'max:2000'],
            'current_skills' => ['nullable', 'string', 'max:2000'],
            'level' => ['required', new Enum(ApplicantLevel::class)],
            'id_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'motivation' => ['required', 'string', 'max:5000'],
        ];
    }
}
