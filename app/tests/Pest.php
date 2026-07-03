<?php

declare(strict_types=1);

use App\Models\Company;
use App\Support\Services\CompanyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)->in('Architecture');

/**
 * One-line tenant context for tests: sets the CompanyContext singleton and
 * the spatie permission team id, exactly like SetCompanyContext middleware.
 */
function setCompany(Company $company): Company
{
    app(CompanyContext::class)->set($company);

    if (function_exists('setPermissionsTeamId')) {
        setPermissionsTeamId($company->id);
    }

    return $company;
}
