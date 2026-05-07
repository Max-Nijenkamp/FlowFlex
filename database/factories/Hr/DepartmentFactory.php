<?php

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'name'        => $this->faker->randomElement(['Engineering', 'HR', 'Finance', 'Sales', 'Marketing', 'Operations', 'Legal']),
            'description' => $this->faker->sentence(),
            'color'       => $this->faker->hexColor(),
        ];
    }
}
