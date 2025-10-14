<?php

namespace App\Jobs;

use App\Models\GuestNotifiable;
use App\Notifications\FirebaseNotification;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFirebaseNotification implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public string $token,
        public string $locale,
        public string $textEn,
        public string $textAr,
    ) {}

    public function handle(): void
    {
        // Check if batch was cancelled
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        try {

            $notifiable = new GuestNotifiable();
            $notifiable->token = $this->token;
            $notifiable->locale = $this->locale;

            // Determine title and body based on locale
            $title = $this->locale === 'en' ? 'New Notification' : 'اشعار جديد';
            $body = $this->locale === 'en' ? $this->textEn : $this->textAr;

            // Send notification
            $notifiable->notify(new FirebaseNotification(
                $this->token,
                $title,
                $body,
            ));

            Log::info('Firebase notification job processed successfully', [
                'token' => substr($this->token, 0, 10) . '...',
                'locale' => $this->locale
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to process Firebase notification job', [
                'token' => substr($this->token, 0, 10) . '...',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger the failed method
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        Log::error('SendFirebaseNotification job failed completely', [
            'token' => substr($this->token, 0, 10) . '...',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
