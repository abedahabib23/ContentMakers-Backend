<?php

namespace App\Http\Requests\Outputs;

use App\Enums\OutputStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;

class ReviewTraineeOutputRequest extends FormRequest
{
    /**
     * Checked here, not just in the controller — see
     * StoreTraineeOutputRequest for why.
     */
    public function authorize(): bool
    {
        return Gate::allows('review', $this->route('output'));
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new Enum(OutputStatus::class)],
            'score' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
