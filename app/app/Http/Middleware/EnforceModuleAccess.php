<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Contracts\BillingServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates non-Filament routes on module activation:
 *   Route::middleware(['auth:sanctum', 'module:hr.payroll'])
 */
class EnforceModuleAccess
{
    public function __construct(
        private readonly BillingServiceInterface $billing,
    ) {}

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        abort_unless($this->billing->hasModule($moduleKey), 403, "Module [{$moduleKey}] is not active.");

        return $next($request);
    }
}
