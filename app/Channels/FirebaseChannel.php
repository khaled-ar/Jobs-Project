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
        Log::info('FirebaseChannel constructor called');
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase-credentials.json'));
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
                ->toToken($messageData['token']) // FIXED: withToken() instead of toToken()
                ->withNotification($fcmNotification)
                ->withData($messageData['data'] ?? []);

            // Send the message first
            $response = $this->messaging->send($message);

            // Then update the guest record (if it exists)
            GuestNotifiable::where('token', $messageData['token'])->update([
                'last_used_at' => now()
            ]);

            Log::info('Firebase notification sent successfully', [
                'message_id' => $response,
                'token' => $messageData['token']
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to send Firebase notification: ' . $e->getMessage(), [
                'token' => $messageData['token'] ?? 'unknown'
            ]);
            throw $e;
        }
    }
}
