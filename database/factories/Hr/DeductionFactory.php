<?php

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\Deduction;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deduction>
 */
class DeductionFactory extends Factory
{
    protected $model = Deduction::class;

    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'employee_id'    => Employee::factory(),
            'name'           => $this->faker->randomElement(['Pension', 'Health Insurance', 'Union Dues', 'Equipment Loan']),
            'deduction_type' => 'fixed',
            'amount'         => $this->faker->randomFloat(2, 10, 500),
            'is_percentage'  => false,
            'is_recurring'   => true,
            'effective_from' => now()->startOfMonth()->format('Y-m-d'),
        ];
    }
}
