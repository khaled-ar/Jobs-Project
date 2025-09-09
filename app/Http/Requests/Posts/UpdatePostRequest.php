<?php

namespace App\Http\Requests\Posts;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        app()->setLocale('ar');
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
            'title' => ['string'],
            'title_ar' => ['string'],
            'text' => ['string'],
            'text_ar' => ['string'],
            'whatsapp' => ['string'],
            'gender' => ['string', 'in:male,female'],
        ];
    }

    public function update($post) {
        $post->update($this->validated());
        return $this->generalResponse(null, 'Updated Successfully', 201);
    }
}
