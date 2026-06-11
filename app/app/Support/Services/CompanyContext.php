<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;

/**
 * Holds the current company for one HTTP request or one queued job.
 * Bound as a singleton in AppServiceProvider.
 */
class CompanyContext
{
    private ?Company $company = null;

    public function set(Company $company): void
    {
        $this->company = $company;
    }

    public function current(): Company
    {
        return $this->company ?? throw new MissingCompanyContextException;
    }

    public function currentId(): ?string
    {
        return $this->company?->id;
    }

    public function has(): bool
    {
        return $this->company !== null;
    }

    public function forget(): void
    {
        $this->company = null;
    }
}
