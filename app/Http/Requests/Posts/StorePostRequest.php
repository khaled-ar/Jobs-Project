<?php

namespace App\Http\Requests\Posts;

use App\Jobs\SendFirebaseNotification;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // app()->setLocale('ar');
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
            'title' => ['required', 'string'],
            'title_ar' => ['required', 'string'],
            'text' => ['required', 'string'],
            'text_ar' => ['required', 'string'],
            'whatsapp' => ['required', 'string'],
            'gender' => ['required', 'string', 'in:male,female'],
        ];
    }

    public function store() {
        $user = request()->user();
        $data = $this->validated();
        $data['status'] = 'pending';

        if($user->role == 'user') {
            if((Setting::where('key', 'post.automatic_approval')->first())->value) {
                $data['status'] = 'active';
                $user->posts()->create($data);
                return $this->generalResponse(null, '201', 201);
            }
            $user->posts()->create($data);
            return $this->generalResponse(null, 'Added successfully, please wait for approval from the administrator', 201);
        }

        Post::create($this->validated());

        // if(Post::count() % 15 == 0) {
        //     SendFirebaseNotification::dispatch();
        // }

        return $this->generalResponse(null, '201', 201);
    }
}
