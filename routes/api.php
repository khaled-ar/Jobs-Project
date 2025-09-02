<?php

use App\Http\Controllers\{
    AuthController,
    AdsController,
    PostsController,
};
use App\Models\Ad;
use App\Models\Post;
use Illuminate\Support\Facades\Route;


Route::middleware('lang')->group(function() {

    Route::apiResource('ads', AdsController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('ads', [AdsController::class, 'index']);
    Route::get('ads/{ad}', [AdsController::class, 'show']);

    Route::apiResource('posts', PostsController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('posts', [PostsController::class, 'index']);
    Route::get('posts/{post}', [PostsController::class, 'show']);
    Route::get('statistics', fn() => ['ads_count' => Ad::count(), 'posts_count' => Post::count()])
        ->middleware('auth:sanctum');

    Route::post('auth/login', [AuthController::class, 'login']);
});
