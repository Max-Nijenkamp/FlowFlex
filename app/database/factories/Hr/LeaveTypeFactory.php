<?php

declare(strict_types=1);

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LeaveType> */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->unique()->word().' leave',
            'accrual_days_per_year' => 25,
            'carry_over_days' => 5,
            'requires_approval' => true,
        ];
    }
}
