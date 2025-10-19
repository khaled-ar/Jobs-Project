<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class EmailVerificationCode extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $code = substr(str_shuffle('0123456789'), 0, 6);
        Cache::put($notifiable->email, $code, 60 * 5);

        return (new MailMessage)
                    ->subject('Password Recovery')
                    ->line('This email has been sent to you because a password reset was requested for your account. If this was you, please confirm the request using the verification code below. If this was not you, simply ignore this email.')
                    ->line("Verification code: ({$code}) It is only valid for five minutes.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
