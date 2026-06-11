<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\DealController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\LeaveRequestController;
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

// REST API v1 — bearer tokens, throttled, module-gated, ability-scoped
// (architecture/api-design). Reads 300/min, writes via api-write limits.
Route::prefix('v1')->middleware(['auth:sanctum', 'throttle:300,1'])->group(function (): void {
    Route::get('/company', fn (Request $request) => response()->json([
        'data' => $request->user()->company->only(['id', 'name', 'slug', 'subscription_status']),
    ]));

    // HR
    Route::middleware('module:hr.profiles')->group(function (): void {
        Route::get('/employees', [EmployeeController::class, 'index'])->middleware('ability:hr:read,hr:write');
        Route::post('/employees', [EmployeeController::class, 'store'])->middleware(['abilities:hr:write', 'throttle:60,1']);
        Route::get('/employees/{id}', [EmployeeController::class, 'show'])->middleware('ability:hr:read,hr:write');
    });
    Route::middleware('module:hr.leave')->group(function (): void {
        Route::get('/leave-requests', [LeaveRequestController::class, 'index'])->middleware('ability:hr:read,hr:write');
        Route::post('/leave-requests', [LeaveRequestController::class, 'store'])->middleware(['abilities:hr:write', 'throttle:60,1']);
        Route::patch('/leave-requests/{id}/approve', [LeaveRequestController::class, 'approve'])->middleware(['abilities:hr:write', 'throttle:60,1']);
        Route::patch('/leave-requests/{id}/reject', [LeaveRequestController::class, 'reject'])->middleware(['abilities:hr:write', 'throttle:60,1']);
    });

    // Finance
    Route::middleware('module:finance.invoicing')->group(function (): void {
        Route::get('/invoices', [InvoiceController::class, 'index'])->middleware('ability:finance:read,finance:write');
        Route::post('/invoices', [InvoiceController::class, 'store'])->middleware(['abilities:finance:write', 'throttle:60,1']);
        Route::post('/invoices/{id}/send', [InvoiceController::class, 'send'])->middleware(['abilities:finance:write', 'throttle:60,1']);
        Route::post('/invoices/{id}/payments', [InvoiceController::class, 'recordPayment'])->middleware(['abilities:finance:write', 'throttle:60,1']);
    });

    // CRM
    Route::middleware('module:crm.contacts')->group(function (): void {
        Route::get('/contacts', [ContactController::class, 'index'])->middleware('ability:crm:read,crm:write');
        Route::post('/contacts', [ContactController::class, 'store'])->middleware(['abilities:crm:write', 'throttle:60,1']);
        Route::get('/contacts/{id}', [ContactController::class, 'show'])->middleware('ability:crm:read,crm:write');
    });
    Route::middleware('module:crm.deals')->group(function (): void {
        Route::get('/deals', [DealController::class, 'index'])->middleware('ability:crm:read,crm:write');
        Route::post('/deals', [DealController::class, 'store'])->middleware(['abilities:crm:write', 'throttle:60,1']);
    });
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
