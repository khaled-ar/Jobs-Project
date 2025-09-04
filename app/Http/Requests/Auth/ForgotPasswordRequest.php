<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Notifications\EmailVerificationCode;
use App\Services\Whatsapp;
use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
            'email' => ['required', 'string', 'exists:users,email']
        ];
    }

    public function send_code() {
        $user = User::whereEmail($this->email)->first();
        $user->notify(new EmailVerificationCode());
        return $this->generalResponse(null, 'Email Check', 200);
    }
}
