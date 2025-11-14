<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'email' => ['required', 'string', 'exists:users,email'],
            'password' => ['required', 'string', 'confirmed',
                Password::min(4)
                    ->max(25)
                    ->uncompromised()
                ]
        ];
    }

    public function reset_password() {
        if(Cache::get($this->email)) {
            return $this->generalResponse(null, 'error_403', 403);
        }

        User::whereEmail($this->email)->update(['password' => Hash::make($this->password)]);
        return $this->generalResponse(null, 'Updated Successfully', 200);
    }
}
