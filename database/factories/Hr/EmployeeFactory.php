<?php

namespace Database\Factories\Hr;

use App\Enums\Hr\EmploymentStatus;
use App\Enums\Hr\EmploymentType;
use App\Models\Company;
use App\Models\Hr\Department;
use App\Models\Hr\Employee;
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
            'company_id'               => Company::factory(),
            'employee_number'          => 'EMP-' . $this->faker->unique()->numerify('####'),
            'first_name'               => $this->faker->firstName(),
            'last_name'                => $this->faker->lastName(),
            'email'                    => $this->faker->unique()->safeEmail(),
            'phone'                    => $this->faker->phoneNumber(),
            'date_of_birth'            => $this->faker->dateTimeBetween('-60 years', '-20 years')->format('Y-m-d'),
            'job_title'                => $this->faker->jobTitle(),
            'location'                 => $this->faker->city(),
            'start_date'               => $this->faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
            'contracted_hours_per_week'=> 40,
            'employment_type'          => $this->faker->randomElement(EmploymentType::cases())->value,
            'employment_status'        => EmploymentStatus::Active->value,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => EmploymentStatus::Active->value,
        ]);
    }

    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'employment_status' => EmploymentStatus::Terminated->value,
        ]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $company->id,
        ]);
    }
}
