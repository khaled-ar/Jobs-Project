<?php

namespace App\Http\Requests\Posts;

use App\Jobs\SendFirebaseNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Bus;

class AcceptPostRequest extends FormRequest
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

        ];
    }

    public function accept($post) {
        // $post->update(['status' => 'active']);
        $notifiable = $post->user;
        $notifiable->locale = substr($notifiable->fcm, 0, 2);
        $notifiable->token = substr($notifiable->fcm, 2);

        dispatch(new SendFirebaseNotification(
            $notifiable->token,
            $notifiable->locale,
            "Job accepted successfully, Job Title {$post->title}",
            "{$post->title_ar} تم قبول الوظيفة بنجاح، عنوان الوظيفة",
            $notifiable
        ));

        return $this->generalResponse(null, null, 200);
    }
}
