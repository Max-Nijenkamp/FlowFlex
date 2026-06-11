<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\CompanyModuleSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyModuleSubscription>
 */
class CompanyModuleSubscriptionFactory extends Factory
{
    protected $model = CompanyModuleSubscription::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'module_key' => 'core.settings',
            'activated_at' => now(),
            'deactivated_at' => null,
            'activated_by' => null,
        ];
    }

    public function module(string $key): static
    {
        return $this->state(fn () => ['module_key' => $key]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }

    public function deactivated(): static
    {
        return $this->state(fn () => ['deactivated_at' => now()]);
    }
}
