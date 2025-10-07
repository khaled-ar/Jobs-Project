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
        public array $notifiableData,
        public string $textEn,
        public string $textAr
    ) {}

    public function handle(): void
    {
        $notifiable = new GuestNotifiable($this->notifiableData);

        $notifiable->notify(new FirebaseNotification(
            $this->notifiableData['token'],
            $this->notifiableData['locale'] == 'en' ? 'New Notification' : 'اشعار جديد',
            $this->notifiableData['locale'] == 'en' ? $this->textEn : $this->textAr,
        ));
    }
    /**
     * Handle a job failure.
     */
    public function failed(\Exception $exception): void
    {
        Log::error('SendFirebaseNotification job failed completely', [
            'error' => $exception->getMessage(),
        ]);
    }
}
