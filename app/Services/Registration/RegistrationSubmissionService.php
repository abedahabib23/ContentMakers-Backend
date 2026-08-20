<?php

namespace App\Services\Registration;

use App\Enums\SubmissionStatus;
use App\Enums\UserType;
use App\Exceptions\Registration\EmailAlreadyRegisteredException;
use App\Exceptions\Registration\RegistrationClosedException;
use App\Exceptions\Registration\SubmissionAlreadyProcessedException;
use App\Models\RegistrationForm;
use App\Models\RegistrationSubmission;
use App\Models\Trainee;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    /**
     * Creates the applicant's account and returns the plaintext password
     * alongside it — this is the only place it ever exists outside the
     * hash, so the caller must hand it to the applicant now.
     *
     * @return array{user: User, password: string}
     */
    public function accept(RegistrationSubmission $submission): array
    {
        $this->ensurePending($submission);

        if (User::where('email', $submission->email)->exists()) {
            throw new EmailAlreadyRegisteredException;
        }

        return DB::transaction(function () use ($submission) {
            $password = Str::password(12);

            $user = User::create([
                'name' => $submission->full_name,
                'email' => $submission->email,
                'password' => $password,
                'type' => UserType::Trainee,
            ]);

            // email_verified_at is deliberately absent from User's
            // $fillable — the admin accepting this application already
            // vouches for the address, same reasoning as UserService::create().
            $user->forceFill(['email_verified_at' => now()])->save();

            Trainee::create([
                'user_id' => $user->id,
                'project_id' => $submission->registrationForm->project_id,
                'registration_submission_id' => $submission->id,
            ]);

            // status is deliberately absent from RegistrationSubmission's
            // #[Fillable] list — nothing should mass-assign it — so
            // update() would silently no-op it here.
            $submission->forceFill(['status' => SubmissionStatus::Accepted])->save();

            return ['user' => $user, 'password' => $password];
        });
    }

    public function reject(RegistrationSubmission $submission): RegistrationSubmission
    {
        $this->ensurePending($submission);

        $submission->forceFill(['status' => SubmissionStatus::Rejected])->save();

        return $submission;
    }

    private function ensurePending(RegistrationSubmission $submission): void
    {
        if ($submission->status !== SubmissionStatus::Pending) {
            throw new SubmissionAlreadyProcessedException;
        }
    }
}
