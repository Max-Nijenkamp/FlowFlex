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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

        // Guests on staff surfaces (Horizon, Pulse, anything under /admin)
        // belong on the STAFF login, not the customer one. Without this,
        // Laravel's default falls back to route('login') — the public site.
        $middleware->redirectGuestsTo(fn (Request $request): string => $request->is('admin', 'admin/*', 'horizon', 'horizon/*', 'pulse', 'pulse/*')
            ? '/admin/login'
            : '/login');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Branded 404 for the public site (Switchboard+ "switched off" page).
        // Panels, Livewire and API keep their own 404 handling.
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            $internal = $request->is('api/*', 'admin*', 'app*', 'hr*', 'finance*', 'crm*', 'livewire*', 'horizon*', 'pulse*', 'up', 'build/*', 'storage/*');

            if ($internal || ! $request->isMethod('GET') || $request->expectsJson()) {
                return null;
            }

            return Inertia\Inertia::render('Marketing/NotFound')
                ->toResponse($request)
                ->setStatusCode(404);
        });
    })->create();
