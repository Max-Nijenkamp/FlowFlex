<?php

declare(strict_types=1);

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Employee> */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_number' => 'EMP-'.str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'hire_date' => now()->subYear()->toDateString(),
            'job_title' => fake()->jobTitle(),
            'employment_type' => 'full-time',
        ];
    }
}
