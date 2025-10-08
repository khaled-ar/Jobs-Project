<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\{
    ForgotPasswordRequest,
    LoginRequest,
    RegisterRequest,
    ResetPasswordRequest,
    VerifyCodeRequest
};


class AuthController extends Controller
{
    public function __construct()
    {
        //app()->setLocale('ar');
    }

    public function register(RegisterRequest $request) {
        return $request->register();
    }

    public function login(LoginRequest $request) {
        return $request->check();
    }

    public function forgot_password(ForgotPasswordRequest $request) {
        return $request->send_code();
    }

    public function verify(VerifyCodeRequest $request) {
        return $request->verify_code();
    }

    public function reset_password(ResetPasswordRequest $request) {
        return $request->reset_password();
    }

    public function resend_code(ForgotPasswordRequest $request) {
        return $request->send_code();
    }

    public function logout() {
        $user = request()->user();
        $user->tokens()->delete();
        return $this->generalResponse(null, 'logout_success', 200);
    }
}
