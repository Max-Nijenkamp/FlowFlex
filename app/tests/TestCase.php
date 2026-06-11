<?php

declare(strict_types=1);

namespace Tests;

use App\Models\Company;
use App\Support\Services\CompanyContext;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Set the tenant company context for a test (mirrors SetCompanyContext middleware):
     * sets CompanyContext + spatie permission team id.
     */
    protected function setCompany(Company $company): Company
    {
        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        return $company;
    }
}
