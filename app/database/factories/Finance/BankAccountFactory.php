<?php

declare(strict_types=1);

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\Account;
use App\Models\Finance\BankAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<BankAccount> */
class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Business account',
            'bank_name' => fake()->randomElement(['ING', 'Rabobank', 'ABN AMRO', 'Bunq']),
            'iban' => 'NL91ABNA0417164300',
            'iban_last4' => '4300',
            'currency' => 'EUR',
            'gl_account_id' => Account::factory()->state(['type' => 'asset', 'code' => (string) fake()->unique()->numberBetween(1100, 1199)]),
        ];
    }
}
