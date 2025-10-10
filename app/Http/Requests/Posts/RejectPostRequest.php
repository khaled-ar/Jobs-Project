<?php

namespace App\Http\Requests\Posts;

use App\Jobs\SendFirebaseNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Bus;

class RejectPostRequest extends FormRequest
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
            'reason_ar' => ['required', 'string', 'max:1000'],
            'reason_en' => ['required', 'string', 'max:1000'],
        ];
    }

    public function reject($post) {
        $notifiable = $post->user;
        $notifiable->locale = substr($notifiable->fcm, 0, 2);
        $notifiable->token = substr($notifiable->fcm, 2);

        dispatch(new SendFirebaseNotification(
            $notifiable->token,
            $notifiable->locale,
            "Job rejected, Job Title {$post->title}, Reject Reason: {$this->reason_en}",
            "مع الاسف تم رفض الوظيفة" . "، سبب الرفض: {$this->reason_ar}",
            $notifiable
        ));

        // $post->delete();
        return $this->generalResponse(null, null, 200);
    }
}
