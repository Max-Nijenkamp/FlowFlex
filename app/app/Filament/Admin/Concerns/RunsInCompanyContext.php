<?php

declare(strict_types=1);

namespace App\Filament\Admin\Concerns;

use App\Models\Company;
use App\Support\Services\CompanyContext;

/**
 * Staff-console helpers that need a tenant context for a single call.
 * Always forgets the context so the admin request stays unscoped.
 */
trait RunsInCompanyContext
{
    protected function withCompanyContext(Company $company, callable $callback): mixed
    {
        $context = app(CompanyContext::class);

        try {
            $context->set($company);
            setPermissionsTeamId($company->id);

            return $callback();
        } finally {
            $context->forget();
            setPermissionsTeamId(null);
        }
    }
}
