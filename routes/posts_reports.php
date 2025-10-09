<?php

use App\Http\Controllers\PostsReportsController;
use Illuminate\Support\Facades\Route;


Route::apiResource('posts-reports', PostsReportsController::class)->except('index')->middleware(['auth:sanctum']);
Route::get('posts-reports', [PostsReportsController::class, 'index'])->middleware(['auth:sanctum', 'admin']);
Route::post('posts-reports/send-answer', [PostsReportsController::class, 'send_answer'])->middleware(['auth:sanctum', 'admin']);

