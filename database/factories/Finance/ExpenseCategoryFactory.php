<?php

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExpenseCategory>
 */
class ExpenseCategoryFactory extends Factory
{
    protected $model = ExpenseCategory::class;

    public function definition(): array
    {
        return [
            'company_id'   => Company::factory(),
            'name'         => $this->faker->unique()->words(2, true),
            'description'  => $this->faker->optional()->sentence(),
            'monthly_limit'=> $this->faker->optional()->randomFloat(2, 100, 5000),
            'is_active'    => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
