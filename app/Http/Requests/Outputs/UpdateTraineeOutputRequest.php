<?php

namespace App\Http\Requests\Outputs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateTraineeOutputRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see
     * StoreTraineeOutputRequest for why.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('output'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'work_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'work_file' => ['sometimes', 'nullable', 'file', 'max:20480'],
            'work_description' => ['sometimes', 'nullable', 'string'],
            'output_date' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
