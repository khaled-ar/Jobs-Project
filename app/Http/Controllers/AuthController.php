<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\VerifyCodeRequest;
use Illuminate\Http\Request;

class AuthController extends Controller
{
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
}
