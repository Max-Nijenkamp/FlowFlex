<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;

/**
 * Request/job-scoped current-company singleton. Fails closed:
 * current() throws rather than ever returning a global view.
 */
class CompanyContext
{
    private ?Company $company = null;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function forget(): void
    {
        $this->company = null;
    }

    public function current(): Company
    {
        return $this->company ?? throw new MissingCompanyContextException;
    }

    public function currentId(): ?string
    {
        return $this->company?->id;
    }
}
