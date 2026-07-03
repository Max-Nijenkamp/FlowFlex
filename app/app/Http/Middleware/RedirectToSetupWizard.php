<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sends fresh companies into the setup wizard. No-ops until core.setup-wizard
 * registers its page route — soft dependency, degrades to nothing.
 */
class RedirectToSetupWizard
{
    private const WIZARD_ROUTE = 'filament.app.pages.setup-wizard';

    public function handle(Request $request, Closure $next): Response
    {
        $context = app(CompanyContext::class);

        if (
            $context->currentId() !== null
            && $context->current()->setup_completed_at === null
            && Route::has(self::WIZARD_ROUTE)
            && ! $request->routeIs(self::WIZARD_ROUTE)
        ) {
            return redirect()->route(self::WIZARD_ROUTE);
        }

        return $next($request);
    }
}
