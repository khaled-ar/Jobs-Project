<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Channels\FirebaseChannel;
use Illuminate\Notifications\Notification;

class FirebaseNotification extends Notification
{
    use Queueable;

    protected $token;
    protected $title;
    protected $body;
    protected $data;

    /**
     * Create a new notification instance.
     *
     * @param string $token Device token (required for guests)
     * @param string $title Notification title.
     * @param string $body Notification body.
     * @param array  $data Optional extra data payload.
     */
    public function __construct(string $token, string $title, string $body, array $data = [])
    {
        $this->token = $token;
        $this->title = $title;
        $this->body = $body;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FirebaseChannel::class];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toFirebase($notifiable)
    {
        return [
            'token' => $this->token,
            'title' => $this->title,
            'body'  => $this->body,
            'data'  => $this->data,
        ];
    }
}
