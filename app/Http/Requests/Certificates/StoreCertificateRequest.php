<?php

namespace App\Http\Requests\Certificates;

use App\Models\Certificate;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCertificateRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', [Certificate::class, $this->route('trainee')]);
    }

    /**
     * certificate_number is deliberately not accepted here — it's
     * generated server-side (see CertificateService), never client-supplied.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'file' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'issued_at' => ['nullable', 'date'],
        ];
    }

    /**
     * One certificate per trainee per project — caught here instead of
     * only at the DB unique constraint, which would otherwise surface as
     * an unhandled 500 rather than a clean validation error.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $trainee = $this->route('trainee');

            $alreadyIssued = Certificate::where('trainee_id', $trainee->id)
                ->where('project_id', $trainee->project_id)
                ->exists();

            if ($alreadyIssued) {
                $validator->errors()->add('trainee_id', __('certificates.already_issued'));
            }
        });
    }
}
