<?php

namespace App\Services\Registration;

use App\Models\Project;
use App\Models\RegistrationForm;
use Illuminate\Support\Str;

class RegistrationFormService
{
    /**
     * @param  array{seats_count: int, deadline: string}  $data
     */
    public function create(Project $project, array $data): RegistrationForm
    {
        return $project->registrationForms()->create([
            ...$data,
            'slug' => $this->generateUniqueSlug(),
        ]);
    }

    private function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(12);
        } while (RegistrationForm::where('slug', $slug)->exists());

        return $slug;
    }
}
