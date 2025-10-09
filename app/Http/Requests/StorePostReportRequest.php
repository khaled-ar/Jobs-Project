<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\Files;

class StorePostReportRequest extends FormRequest
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
            'post_id' => ['required', 'integer', 'exists:posts,id'],
            'image' => ['required', 'image', 'mimes:png,jpg', 'max:2048']
        ];
    }

    public function store() {
        $data = $this->validated();
        $data['image'] = Files::moveFile($this->image, 'Images/PostsReports');
        request()->user()->posts_reports()->create($data);
        return $this->generalResponse(null, '201', 201);
    }
}
