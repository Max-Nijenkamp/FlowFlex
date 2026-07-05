<?php

declare(strict_types=1);

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Department> */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->unique()->word().' team',
        ];
    }
}
