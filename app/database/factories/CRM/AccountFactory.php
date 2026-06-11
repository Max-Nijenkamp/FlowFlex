<?php

declare(strict_types=1);

namespace Database\Factories\CRM;

use App\Models\Company;
use App\Models\CRM\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->company(),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }
}
