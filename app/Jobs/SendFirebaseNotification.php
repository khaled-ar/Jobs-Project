<?php

namespace App\Jobs;

use App\Models\GuestNotifiable;
use App\Notifications\FirebaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFirebaseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The number of seconds the job can run before timing out.
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting Firebase notification job');

            // Use chunking for large datasets to avoid memory issues
            GuestNotifiable::select(['token', 'locale', 'last_used_at'])
                ->whereNotNull('token')
                ->where('token', '!=', '')
                ->chunk(100, function ($notifiables) {
                    foreach ($notifiables as $notifiable) {
                        $this->sendNotification($notifiable);
                    }
                });

            Log::info('Firebase notification job completed successfully');

        } catch (\Exception $e) {
            Log::error('Firebase notification job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // This will trigger retries
        }
    }

    /**
     * Send notification to a single notifiable.
     */
    protected function sendNotification($notifiable): void
    {
        try {
            $title = $body = '';

            if ($notifiable->locale == 'ar') {
                $title = 'منشورات جديدة';
                $body = 'لقد تم اضافة العديد من المنشورات الجديدة';
            } else {
                $title = 'New Posts';
                $body = 'Many new posts have been added.';
            }

            $notifiable->notify(new FirebaseNotification(
                $notifiable->token,
                $title,
                $body,
                ['type' => 'new_posts', 'sent_at' => now()->toISOString()]
            ));

            Log::debug('Notification sent', [
                'token' => $notifiable->token,
                'locale' => $notifiable->locale
            ]);

        } catch (\Exception $e) {
            Log::warning('Failed to send notification to token', [
                'token' => $notifiable->token,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        Log::error('SendFirebaseNotification job failed completely', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
