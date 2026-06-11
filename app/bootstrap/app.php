<?php

declare(strict_types=1);

use App\Http\Middleware\EnforceModuleAccess;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetCompanyContextFromToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'module' => EnforceModuleAccess::class,
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);

        // Inertia shared props for the public Vue site.
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        // API requests authenticate via Sanctum bearer tokens — tenant context
        // resolves from the token's user on every /api request.
        $middleware->api(append: [
            SetCompanyContextFromToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
