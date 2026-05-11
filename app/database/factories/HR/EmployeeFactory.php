<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'employee_number'  => 'EMP-' . str_pad((string) fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'first_name'       => fake()->firstName(),
            'last_name'        => fake()->lastName(),
            'email'            => fake()->unique()->safeEmail(),
            'phone'            => fake()->optional()->phoneNumber(),
            'date_of_birth'    => fake()->optional()->dateTimeBetween('-60 years', '-18 years')?->format('Y-m-d'),
            'hire_date'        => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'employment_type'  => fake()->randomElement(['full_time', 'part_time', 'contractor', 'intern']),
            'department'       => fake()->optional()->randomElement(['Engineering', 'HR', 'Finance', 'Marketing', 'Sales', 'Operations']),
            'job_title'        => fake()->optional()->jobTitle(),
            'location'         => fake()->optional()->city(),
            'status'           => 'active',
        ];
    }

    public function terminated(): static
    {
        return $this->state([
            'status'           => 'terminated',
            'termination_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
        ]);
    }

    public function onLeave(): static
    {
        return $this->state(['status' => 'on_leave']);
    }
}
