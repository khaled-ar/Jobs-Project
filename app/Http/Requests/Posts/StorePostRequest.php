<?php

namespace App\Http\Requests\Posts;

use App\Jobs\SendFirebaseNotification;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StorePostRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'title_ar' => ['required', 'string'],
            'text' => ['required', 'string'],
            'text_ar' => ['required', 'string'],
            'whatsapp' => ['required', 'string'],
            'gender' => ['required', 'string', 'in:male,female'],
        ];
    }

    public function store() {

        Post::create($this->validated());

        if(Post::count() % 15 == 0) {
            SendFirebaseNotification::dispatch();
        }

        return $this->generalResponse(null, '201', 201);
    }
}
