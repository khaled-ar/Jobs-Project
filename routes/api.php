<?php

use App\Http\Controllers\{
    AdsController,
    NotifiablesController,
    UpdatesController,
};

use Illuminate\Support\Facades\Route;
use App\Services\GoogleTranslateService;

Route::post('translate', function() {
    $service = new GoogleTranslateService();
    return $service->translate('hi my name is khaled', 'fr', 'en');
});

Route::prefix('firebase')->controller(NotifiablesController::class)->group(function() {
    Route::post('', 'store_or_update');
    Route::post('send-firebase-notification', 'send')->middleware('auth:sanctum');
});

Route::middleware('lang')->group(function() {
    Route::apiResource('ads', AdsController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('ads', [AdsController::class, 'index']);
    Route::apiResource('updates', UpdatesController::class)->except(['index', 'show'])->middleware('auth:sanctum');
    Route::get('updates', [UpdatesController::class, 'index']);

    // Posts Reports Routes
    include base_path('/routes/posts_reports.php');

    // Users Routes
    include base_path('/routes/users.php');

    // Posts Routes
    include base_path('/routes/posts.php');

    // Auth Routes
    include base_path('/routes/auth.php');

});
