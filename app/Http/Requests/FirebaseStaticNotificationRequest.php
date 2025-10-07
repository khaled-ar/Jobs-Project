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

        GuestNotifiable::select(['token', 'locale', 'last_used_at'])
            ->whereNotNull('token')
            ->where('token', '!=', '')
            ->where('last_used_at', '>', now()->subDays(30))
            ->chunk(100, function ($notifiables) use (&$jobs) {
                foreach ($notifiables as $notifiable) {
                    $jobs[] = new SendFirebaseNotification(
                        $notifiable->toArray(),
                        $this->text_en,
                        $this->text_ar
                    );
                }
            });

        // Dispatch all jobs in batches
        if (!empty($jobs)) {
            Bus::batch($jobs)
                ->name('Static Notification')
                ->dispatch();
        }

        return $this->generalResponse(null, '200', 200);

    }
}
