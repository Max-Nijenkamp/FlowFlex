<?php

declare(strict_types=1);

namespace App\Support\Jobs\Middleware;

use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;

/**
 * Queue workers have no HTTP request, so the context singleton is empty.
 * Restores it from the job's (or its event's) company_id before handling.
 * Mandatory on every job/listener touching tenant models.
 */
class WithCompanyContext
{
    public function handle(mixed $job, callable $next): void
    {
        $companyId = $job->event->company_id ?? $job->company_id ?? null;

        if ($companyId !== null) {
            $company = Company::query()->withoutGlobalScope(CompanyScope::class)->findOrFail($companyId);

            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        $next($job);
    }
}
