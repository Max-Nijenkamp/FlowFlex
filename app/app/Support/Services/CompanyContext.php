<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;

class CompanyContext
{
    private ?Company $company = null;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function current(): Company
    {
        return $this->company ?? throw new MissingCompanyContextException();
    }

    public function currentId(): ?string
    {
        return $this->company?->id;
    }

    public function hasCompany(): bool
    {
        return $this->company !== null;
    }

    public function clear(): void
    {
        $this->company = null;
    }
}
