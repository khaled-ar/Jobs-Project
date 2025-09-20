<?php

namespace App\Http\Requests;

use App\Models\Update;
use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateLinkRequest extends FormRequest
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
            'link' => ['required', 'string', 'max:1000', 'unique:updates,link']
        ];
    }

    public function store() {
        $data = $this->validated();
        $data['counter'] = Update::count() + 2;
        Update::create($data);
        return $this->generalResponse(null, '201', 201);
    }
}
