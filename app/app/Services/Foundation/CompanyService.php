<?php

declare(strict_types=1);

namespace App\Services\Foundation;

use App\Contracts\Foundation\CompanyServiceInterface;
use App\Data\Foundation\CreateCompanyData;
use App\Data\Foundation\UpdateCompanyData;
use App\Models\Company;
use Illuminate\Pagination\LengthAwarePaginator;

class CompanyService implements CompanyServiceInterface
{
    public function __construct(
        private readonly CompanyCreationService $creationService,
    ) {}

    public function list(int $perPage = 25): LengthAwarePaginator
    {
        return Company::withoutGlobalScopes()
            ->withTrashed()
            ->with(['moduleSubscriptions'])
            ->withCount(['users', 'moduleSubscriptions'])
            ->latest()
            ->paginate($perPage);
    }

    public function find(string $id): Company
    {
        return Company::withoutGlobalScopes()->findOrFail($id);
    }

    public function create(CreateCompanyData $data): Company
    {
        return $this->creationService->create($data);
    }

    public function update(string $id, UpdateCompanyData $data): Company
    {
        $company = $this->find($id);

        $company->update([
            'name'      => $data->name,
            'slug'      => $data->slug,
            'email'     => $data->email,
            'timezone'  => $data->timezone,
            'locale'    => $data->locale,
            'currency'  => $data->currency,
            'branding'  => $data->branding,
            'ai_config' => $data->ai_config,
        ]);

        return $company->fresh();
    }

    public function suspend(string $id): Company
    {
        $company = $this->find($id);
        $company->update(['status' => 'suspended']);

        return $company->fresh();
    }

    public function activate(string $id): Company
    {
        $company = $this->find($id);
        $company->update([
            'status'       => 'active',
            'subscribed_at' => $company->subscribed_at ?? now(),
        ]);

        return $company->fresh();
    }

    public function cancel(string $id): Company
    {
        $company = $this->find($id);
        $company->update(['status' => 'cancelled']);

        return $company->fresh();
    }
}
