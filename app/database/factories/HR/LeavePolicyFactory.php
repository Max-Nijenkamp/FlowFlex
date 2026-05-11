<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\LeavePolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeavePolicy>
 */
class LeavePolicyFactory extends Factory
{
    protected $model = LeavePolicy::class;

    public function definition(): array
    {
        return [
            'name'              => fake()->randomElement(['Annual Leave', 'Sick Leave', 'Maternity Leave', 'Paternity Leave']),
            'leave_type'        => fake()->randomElement(['annual', 'sick', 'maternity', 'paternity', 'unpaid', 'other']),
            'days_per_year'     => fake()->randomFloat(1, 5, 30),
            'carry_over_days'   => fake()->randomFloat(1, 0, 10),
            'is_paid'           => true,
            'requires_approval' => true,
            'min_notice_days'   => fake()->numberBetween(0, 14),
            'is_active'         => true,
        ];
    }

    public function annual(): static
    {
        return $this->state([
            'name'          => 'Annual Leave',
            'leave_type'    => 'annual',
            'days_per_year' => 25.0,
        ]);
    }

    public function sick(): static
    {
        return $this->state([
            'name'          => 'Sick Leave',
            'leave_type'    => 'sick',
            'days_per_year' => 10.0,
            'min_notice_days' => 0,
        ]);
    }
}
