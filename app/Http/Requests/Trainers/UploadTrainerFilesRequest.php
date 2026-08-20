<?php

namespace App\Http\Requests\Trainers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UploadTrainerFilesRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — FormRequest validation
     * runs before the controller method body, so an unauthorized caller
     * would otherwise see validation error details before ever being told
     * they're not allowed.
     */
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('trainer'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id_photo' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'cv' => ['sometimes', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'certificates' => ['sometimes', 'array', 'max:10'],
            'certificates.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
