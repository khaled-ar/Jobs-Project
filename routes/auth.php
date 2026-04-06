<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->controller(AuthController::class)->group(function() {
        Route::post('login', 'login');
        Route::post('register', 'register');
        Route::post('forgot-password', 'forgot_password');
        Route::post('reset-password', 'reset_password');
        Route::post('verify', 'verify');
        Route::post('resend-code', 'resend_code')->middleware('throttle:1,1');
        Route::post('logout', 'logout')->middleware('auth:sanctum');
        Route::delete('delete-account', 'delete_account')->middleware('auth:sanctum');
        Route::post('upload-cv', 'upload_cv')->middleware('auth:sanctum');
        Route::get('cv', 'cv_path')->middleware('auth:sanctum');
    });
