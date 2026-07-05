<?php

declare(strict_types=1);

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'payment_terms_days' => 14,
        ];
    }
}
