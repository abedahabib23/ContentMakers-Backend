<?php

namespace App\Http\Requests\Certificates;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateCertificateRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see
     * StoreCertificateRequest for why.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('certificate'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['sometimes', 'nullable', 'file', 'mimes:pdf', 'max:10240'],
            'issued_at' => ['sometimes', 'date'],
        ];
    }
}
