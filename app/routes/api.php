<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\ProjectController;
use App\Http\Controllers\Api\V1\TaskController;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

RateLimiter::for('api', function (Request $request) {
    return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)
        ->by($request->user()?->id ?: $request->ip());
});

Route::prefix('v1')->middleware('throttle:api')->group(function (): void {
    Route::post('auth/token', [AuthController::class, 'issueToken']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::delete('auth/token', [AuthController::class, 'revokeToken']);

        Route::apiResource('projects', ProjectController::class);
        Route::apiResource('tasks', TaskController::class);
        Route::apiResource('employees', EmployeeController::class);
    });
});
