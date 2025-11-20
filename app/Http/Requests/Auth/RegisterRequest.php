<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use App\Notifications\VerifyAccountNotification;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'phone' =>  ['string', 'max:50', 'unique:users,phone'],
            'email' =>  ['required', 'email', 'max:50', 'unique:users,email'],
            'password' => ['required', 'string',
                Password::min(4)
                    ->max(25)
                ]
        ];
    }

    public function register() {
        $user = User::create($this->validated());
        $user->notify(new VerifyAccountNotification());
        return $this->generalResponse(null, 'Email Check', 201);
    }
}
