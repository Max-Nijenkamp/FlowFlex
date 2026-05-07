<?php

namespace Database\Factories\Finance;

use App\Enums\Finance\ExpenseStatus;
use App\Models\Company;
use App\Models\Finance\Expense;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'company_id'   => Company::factory(),
            'tenant_id'    => Tenant::factory(),
            'description'  => $this->faker->sentence(3),
            'amount'       => $this->faker->randomFloat(2, 10, 2000),
            'currency'     => 'EUR',
            'expense_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'status'       => ExpenseStatus::Pending->value,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::Approved->value,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'           => ExpenseStatus::Rejected->value,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
