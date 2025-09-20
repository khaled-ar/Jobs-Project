<?php

namespace App\Http\Requests;

use App\Models\GuestNotifiable;
use App\Notifications\FirebaseNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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

        GuestNotifiable::select(['token', 'locale', 'last_used_at'])
            ->whereNotNull('token')
            ->where('token', '!=', '')
            ->where('last_used_at', '>', now()->subDays(30))
            ->chunk(100, function ($notifiables) {
                foreach ($notifiables as $notifiable) {
                    $this->send_notification($notifiable);
                }
            });

        return $this->generalResponse(null, '200', 200);
    }

    public function send_notification($notifiable) {

        try {

            $notifiable->notify(new FirebaseNotification(
                $notifiable->token,
                $notifiable->locale == 'en' ? 'New Notification' : 'اشعار جديد',
                $notifiable->locale == 'en' ? $this->text_en : $this->text_ar,
            ));

            Log::debug('Notification sent', [
                'token' => $notifiable->token,
                'locale' => $notifiable->locale
            ]);

        }catch(\Exception $e) {
            Log::warning('(Static Notification): Failed to send notification to token', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
