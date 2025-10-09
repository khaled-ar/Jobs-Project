<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;


Route::apiResource('users', UsersController::class)->middleware(['auth:sanctum', 'admin']);

