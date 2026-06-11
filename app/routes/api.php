<?php

declare(strict_types=1);

use App\Http\Controllers\Webhooks\ResendWebhookController;
use App\Http\Middleware\VerifyResendSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Resend bounce/complaint webhook — signature-verified + throttled, no session/CSRF.
Route::post('/resend/webhook', ResendWebhookController::class)
    ->middleware([VerifyResendSignature::class, 'throttle:60,1'])
    ->name('webhooks.resend');
