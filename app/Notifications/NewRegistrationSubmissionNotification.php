<?php

namespace App\Notifications;

use App\Models\RegistrationSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewRegistrationSubmissionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly RegistrationSubmission $submission) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $project = $this->submission->registrationForm->project;

        return (new MailMessage)
            ->subject(__('notifications.new_submission_subject', ['project' => $project->name]))
            ->greeting(__('notifications.new_submission_greeting'))
            ->line(__('notifications.new_submission_line', [
                'name' => $this->submission->full_name,
                'project' => $project->name,
            ]))
            ->action(
                __('notifications.new_submission_action'),
                rtrim(config('app.frontend_url'), '/')."/projects/{$project->id}",
            );
    }
}
