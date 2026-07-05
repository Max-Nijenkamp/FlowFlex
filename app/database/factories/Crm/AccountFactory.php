<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Account> */
class AccountFactory extends Factory
{
    protected $model = Account::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->company(),
            'industry' => fake()->randomElement(['SaaS', 'Retail', 'Manufacturing', 'Consulting']),
            'owner_id' => User::factory(),
        ];
    }
}
