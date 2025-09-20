<?php

use App\Http\Controllers\{
    AuthController,
    AdsController,
    NotifiablesController,
    PostsController,
    UpdatesController,
};
use App\Models\{
    Ad,
    Post
};
use Illuminate\Support\Facades\Route;

Route::prefix('firebase')->controller(NotifiablesController::class)->group(function() {
    Route::post('', 'store_or_update');
    Route::post('send-firebase-notification', 'send')->middleware('auth:sanctum');
});

Route::middleware('lang')->group(function() {
    Route::apiResource('ads', AdsController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('ads', [AdsController::class, 'index']);
    Route::apiResource('updates', UpdatesController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('updates', [UpdatesController::class, 'index']);

    Route::apiResource('posts', PostsController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('posts', [PostsController::class, 'index']);
    Route::get('visitor/posts', [PostsController::class, 'all_for_visitor'])->name('visitor.posts');
    Route::get('statistics', fn() => ['ads_count' => Ad::count(), 'posts_count' => Post::count()])
        ->middleware('auth:sanctum');

    Route::prefix('auth')->controller(AuthController::class)->group(function() {
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgot_password');
        Route::post('reset-password', 'reset_password');
        Route::post('verify', 'verify');
        Route::post('resend-code', 'resend_code')->middleware('throttle:1,1');
        Route::post('logout', 'logout')->middleware('auth:sanctum');
    });

});
