<?php

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Middleware\AuthenticateApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'ok', 'version' => '1.0']));

    Route::middleware(AuthenticateApiKey::class)->group(function () {
        Route::get('/me', [ApiController::class, 'me']);
        Route::get('/modules', [ApiController::class, 'modules']);
    });
});
