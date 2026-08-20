<?php

namespace App\Http\Requests\Outputs;

use App\Models\TraineeOutput;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreTraineeOutputRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('create', [TraineeOutput::class, $this->route('trainee')]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:255'],
            'work_url' => ['nullable', 'url', 'max:2048'],
            'work_file' => ['nullable', 'file', 'max:20480'],
            'work_description' => ['nullable', 'string'],
            'output_date' => ['nullable', 'date'],
        ];
    }
}
