<?php

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Middleware\AuthenticateApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:60,1')->get('/health', fn () => response()->json(['status' => 'ok', 'version' => '1.0']));

    Route::middleware(AuthenticateApiKey::class)->group(function () {
        Route::get('/me', [ApiController::class, 'me']);
        Route::get('/modules', [ApiController::class, 'modules']);

        // HR
        Route::prefix('hr')->group(function () {
            Route::get('/employees', [\App\Http\Controllers\Api\V1\Hr\EmployeeController::class, 'index']);
            Route::get('/employees/{id}', [\App\Http\Controllers\Api\V1\Hr\EmployeeController::class, 'show']);
            Route::get('/leave-requests', [\App\Http\Controllers\Api\V1\Hr\LeaveRequestController::class, 'index']);
            Route::get('/leave-requests/{id}', [\App\Http\Controllers\Api\V1\Hr\LeaveRequestController::class, 'show']);
        });

        // Projects
        Route::prefix('projects')->group(function () {
            Route::get('/tasks', [\App\Http\Controllers\Api\V1\Projects\TaskController::class, 'index']);
            Route::get('/tasks/{id}', [\App\Http\Controllers\Api\V1\Projects\TaskController::class, 'show']);
            Route::get('/time-entries', [\App\Http\Controllers\Api\V1\Projects\TimeEntryController::class, 'index']);
            Route::get('/time-entries/{id}', [\App\Http\Controllers\Api\V1\Projects\TimeEntryController::class, 'show']);
        });

        // Finance
        Route::prefix('finance')->group(function () {
            Route::get('/invoices', [\App\Http\Controllers\Api\V1\Finance\InvoiceController::class, 'index']);
            Route::get('/invoices/{id}', [\App\Http\Controllers\Api\V1\Finance\InvoiceController::class, 'show']);
            Route::get('/expenses', [\App\Http\Controllers\Api\V1\Finance\ExpenseController::class, 'index']);
            Route::get('/expenses/{id}', [\App\Http\Controllers\Api\V1\Finance\ExpenseController::class, 'show']);
        });

        // CRM
        Route::prefix('crm')->group(function () {
            Route::get('/contacts', [\App\Http\Controllers\Api\V1\Crm\ContactController::class, 'index']);
            Route::get('/contacts/{id}', [\App\Http\Controllers\Api\V1\Crm\ContactController::class, 'show']);
            Route::get('/deals', [\App\Http\Controllers\Api\V1\Crm\DealController::class, 'index']);
            Route::get('/deals/{id}', [\App\Http\Controllers\Api\V1\Crm\DealController::class, 'show']);
            Route::get('/tickets', [\App\Http\Controllers\Api\V1\Crm\TicketController::class, 'index']);
            Route::get('/tickets/{id}', [\App\Http\Controllers\Api\V1\Crm\TicketController::class, 'show']);
        });
    });
});
