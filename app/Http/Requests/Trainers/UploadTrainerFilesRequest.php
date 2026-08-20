<?php

namespace App\Http\Requests\Trainers;

use Illuminate\Foundation\Http\FormRequest;

class UploadTrainerFilesRequest extends FormRequest
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
            'id_photo' => ['sometimes', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'cv' => ['sometimes', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'certificates' => ['sometimes', 'array', 'max:10'],
            'certificates.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ];
    }
}
