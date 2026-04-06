<?php

namespace App\Http\Requests;

use App\Traits\Files;
use Illuminate\Foundation\Http\FormRequest;

class UploadCvRequest extends FormRequest
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
            'cv' => ['required', 'file', 'mimes:pdf']
        ];
    }

    public function upload() {
        $user = $this->user();
        $user->update([
            'cv' => Files::moveFile($this->cv, 'Cvs', $user->id)
        ]);
        return $this->generalResponse(null);
    }
}
