<?php

namespace App\Http\Requests;

use App\Models\GuestNotifiable;
use App\Notifications\FirebaseNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendFirebaseNotification;

class FirebaseStaticNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'text_en' => ['required', 'string', 'max:1000'],
            'text_ar' => ['required', 'string', 'max:1000'],
        ];
    }

    public function send() {
        $jobs = [];
        $totalUsers = 0;

        GuestNotifiable::select(['token', 'locale', 'last_used_at'])
            ->whereNotNull('token')
            ->where('token', '!=', '')
            ->where('last_used_at', '>', now()->subDays(30))
            ->chunk(100, function ($notifiables) use (&$jobs, &$totalUsers) {
                $totalUsers += $notifiables->count();
                foreach ($notifiables as $notifiable) {
                    $jobs[] = new SendFirebaseNotification(
                        $notifiable->token,
                        $notifiable->locale,
                        $this->text_en,
                        $this->text_ar
                    );
                }
            });

        Log::info('Preparing to send notifications', [
            'total_users' => $totalUsers,
            'jobs_created' => count($jobs)
        ]);

        if (!empty($jobs)) {
            $batch = Bus::batch($jobs)
                ->name('Static Notification')
                ->then(function () use ($totalUsers) {
                    Log::info('All notifications sent successfully', [
                        'total_users' => $totalUsers
                    ]);
                })
                ->catch(function (\Throwable $e) {
                    Log::error('Notification batch failed', [
                        'error' => $e->getMessage()
                    ]);
                })
                ->dispatch();

            Log::info('Notification batch dispatched', [
                'batch_id' => $batch->id,
                'job_count' => count($jobs)
            ]);
        }

        return $this->generalResponse(null, '200', 200);
    }

}
