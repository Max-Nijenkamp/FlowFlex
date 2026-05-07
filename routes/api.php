<?php

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Middleware\AuthenticateApiKey;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/health', fn () => response()->json(['status' => 'ok', 'version' => '1.0']));

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
    });
});
