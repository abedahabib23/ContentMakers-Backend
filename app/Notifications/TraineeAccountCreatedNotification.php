<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TraineeAccountCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $password) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.account_created_subject'))
            ->greeting(__('notifications.account_created_greeting', ['name' => $notifiable->name]))
            ->line(__('notifications.account_created_line'))
            ->line(__('notifications.account_created_email', ['email' => $notifiable->email]))
            ->line(__('notifications.account_created_password', ['password' => $this->password]))
            ->line(__('notifications.account_created_advice'))
            ->action(__('notifications.account_created_action'), rtrim(config('app.frontend_url'), '/').'/login');
    }
}
