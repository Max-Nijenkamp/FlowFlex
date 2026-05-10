<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Core\BillingService;
use App\Support\Services\CompanyContext;
use Closure;
use Illuminate\Http\Request;

class EnforceModuleAccess
{
    public function __construct(
        private readonly BillingService $billingService,
        private readonly CompanyContext $companyContext,
    ) {}

    public function handle(Request $request, Closure $next, string $module): mixed
    {
        if ($this->companyContext->hasCompany()) {
            $allowed = $this->billingService->enforceModuleAccess(
                $this->companyContext->current(),
                $module,
            );

            if (! $allowed) {
                abort(403, 'Module not active for this company.');
            }
        }

        return $next($request);
    }
}
