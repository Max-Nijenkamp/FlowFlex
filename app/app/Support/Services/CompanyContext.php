<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Models\Company;

class CompanyContext
{
    private ?Company $company = null;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function get(): Company
    {
        if ($this->company === null) {
            throw new \RuntimeException('CompanyContext has not been set for this request.');
        }

        return $this->company;
    }

    public function id(): string
    {
        return $this->get()->id;
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
