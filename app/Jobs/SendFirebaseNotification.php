<?php

namespace App\Jobs;

use App\Models\GuestNotifiable;
use App\Notifications\FirebaseNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendFirebaseNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $notifiables = GuestNotifiable::all(['token', 'locale', 'last_used_at']);
        foreach($notifiables as $notifiable) {
            $title = $body = '';
            if($notifiable->locale == 'ar') {
                $title = 'منشورات جديدة';
                $body = 'لقد تم اضافة العديد من النشورات الجديدة';
            } else {
                $title = 'New Posts';
                $body = 'Many new posts have been added.';
            }
            $notifiable->notify(new FirebaseNotification($notifiable->token, $title, $body));
        }
    }
}
