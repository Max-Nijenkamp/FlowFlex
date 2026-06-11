<?php

declare(strict_types=1);

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankAccount>
 */
class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Main account',
            'bank_name' => 'Demo Bank',
            'currency' => 'EUR',
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }
}
