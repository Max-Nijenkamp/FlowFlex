<?php

declare(strict_types=1);

namespace App\Support\Jobs\Middleware;

use App\Models\Company;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;

/**
 * Restores tenant company context inside a queued job/listener.
 * Resolves company_id from $job->event->company_id or $job->company_id.
 * Jobs without a company_id pass through untouched (no crash).
 */
class WithCompanyContext
{
    public function handle(mixed $job, callable $next): void
    {
        $companyId = $job->event->company_id ?? $job->company_id ?? null;

        if ($companyId) {
            $company = Company::query()->withoutGlobalScope(CompanyScope::class)->findOrFail($companyId);
            app(CompanyContext::class)->set($company);
            setPermissionsTeamId($company->id);
        }

        $next($job);
    }
}
