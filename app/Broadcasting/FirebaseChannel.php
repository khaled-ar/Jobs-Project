<?php

namespace App\Channels;

use App\Models\GuestNotifiable;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FirebaseChannel
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase.json'));
        $this->messaging = $factory->createMessaging();
    }

    public function send($notifiable, $notification)
    {
        if (! method_exists($notification, 'toFirebase')) {
            return;
        }

        try {
            $messageData = $notification->toFirebase($notifiable);

            $fcmNotification = FcmNotification::create(
                $messageData['title'],
                $messageData['body']
            );

            $message = CloudMessage::new()
                ->toToken($messageData['token'])
                ->withNotification($fcmNotification)
                ->withData($messageData['data'] ?? []);

            GuestNotifiable::whereToken($messageData['token'])->update([
                'last_used_at' => now()
            ]);

            $this->messaging->send($message);

        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification: ' . $e->getMessage());
            throw $e;
        }
    }
}
