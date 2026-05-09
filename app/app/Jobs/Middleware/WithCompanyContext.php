<?php

declare(strict_types=1);

namespace App\Jobs\Middleware;

use App\Models\Company;
use App\Support\Services\CompanyContext;
use Closure;

/**
 * Sets CompanyContext for the duration of a queued job, then clears it.
 *
 * Queue workers are long-lived singleton processes. Without explicit setup and
 * teardown the CompanyContext singleton leaks between jobs processed by the
 * same worker, causing wrong-tenant data reads or MissingCompanyContextException.
 *
 * Usage:
 *   public function middleware(): array
 *   {
 *       return [new WithCompanyContext($this->companyId)];
 *   }
 */
class WithCompanyContext
{
    public function __construct(private readonly string $companyId) {}

    public function handle(object $job, Closure $next): void
    {
        $context = app(CompanyContext::class);

        $company = Company::withoutGlobalScopes()->findOrFail($this->companyId);
        $context->set($company);
        setPermissionsTeamId($this->companyId);

        try {
            $next($job);
        } finally {
            $context->clear();
            setPermissionsTeamId(null);
        }
    }
}
