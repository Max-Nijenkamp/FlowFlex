<?php

declare(strict_types=1);

use App\Http\Controllers\Webhooks\ResendWebhookController;
use App\Http\Middleware\VerifyResendSignature;
use Illuminate\Support\Facades\Route;

Route::post('/resend/webhook', ResendWebhookController::class)
    ->middleware([VerifyResendSignature::class, 'throttle:60,1'])
    ->name('webhooks.resend');
