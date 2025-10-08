<?php

use App\Http\Controllers\PostsController;
use App\Models\{
    Ad,
    Post
};
use Illuminate\Support\Facades\Route;


Route::apiResource('posts', PostsController::class)->except(['index', 'show'])->middleware(['auth:sanctum', 'admin']);
Route::controller(PostsController::class)->group(function() {
    Route::get('posts', 'index')->middleware(['auth:sanctum', 'admin']);
    Route::get('visitor/posts', 'all_for_visitor')->name('visitor.posts');
    Route::post('visitor/posts', 'add_post')->middleware('auth:sanctum');
});

Route::get('statistics', fn() => ['ads_count' => Ad::count(), 'posts_count' => Post::count()])
    ->middleware('auth:sanctum');
