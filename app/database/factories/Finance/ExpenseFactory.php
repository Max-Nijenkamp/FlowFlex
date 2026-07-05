<?php

declare(strict_types=1);

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Expense> */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'amount_cents' => fake()->numberBetween(500, 50_000),
            'currency' => 'EUR',
            'expense_date' => now()->subDays(2)->toDateString(),
            'merchant' => fake()->company(),
        ];
    }
}
