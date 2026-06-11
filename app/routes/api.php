<?php

declare(strict_types=1);

use App\Http\Controllers\Webhooks\ResendWebhookController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use App\Http\Middleware\VerifyResendSignature;
use App\Http\Middleware\VerifyStripeSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Health\Http\Controllers\SimpleHealthCheckController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// REST API v1 — bearer tokens, throttled; per-module gating via `module:` and
// ability scoping via `abilities:` as endpoints land with their domains.
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:300,1'])->group(function (): void {
    Route::get('/company', fn (Request $request) => response()->json([
        'data' => $request->user()->company->only(['id', 'name', 'slug', 'subscription_status']),
    ]));
});

// Resend bounce/complaint webhook — signature-verified + throttled, no session/CSRF.
Route::post('/resend/webhook', ResendWebhookController::class)
    ->middleware([VerifyResendSignature::class, 'throttle:60,1'])
    ->name('webhooks.resend');

// Health endpoint — minimal JSON, throttled (security notes: no detail leak to anonymous callers).
Route::get('/health', SimpleHealthCheckController::class)
    ->middleware('throttle:30,1')
    ->name('health');

// Stripe billing webhook — signature-verified + throttled.
Route::post('/stripe/webhook', StripeWebhookController::class)
    ->middleware([VerifyStripeSignature::class, 'throttle:60,1'])
    ->name('webhooks.stripe');
