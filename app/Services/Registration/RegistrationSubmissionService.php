<?php

namespace App\Services\Registration;

use App\Exceptions\Registration\RegistrationClosedException;
use App\Models\RegistrationForm;
use App\Models\RegistrationSubmission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegistrationSubmissionService
{
    // Applicant documents — the `private` disk, same as trainer documents.
    public const DISK = 'local';

    /**
     * @param  array<string, mixed>  $data
     */
    public function submit(RegistrationForm $form, array $data): RegistrationSubmission
    {
        return DB::transaction(function () use ($form, $data) {
            // Locked so two near-simultaneous submissions on the last open
            // seat can't both pass the isOpen() check before either commits.
            $locked = RegistrationForm::whereKey($form->id)->lockForUpdate()->firstOrFail();

            if (! $locked->isOpen()) {
                throw new RegistrationClosedException;
            }

            $data['id_photo_path'] = $data['id_photo']->store("registration-forms/{$locked->id}", self::DISK);
            $data['cv_path'] = $data['cv']->store("registration-forms/{$locked->id}", self::DISK);
            unset($data['id_photo'], $data['cv']);

            return $locked->submissions()->create($data);
        });
    }
}
