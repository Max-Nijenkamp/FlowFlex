<?php

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [
            'company_id'        => Company::factory(),
            'name'              => $this->faker->randomElement(['Annual Leave', 'Sick Leave', 'Compassionate Leave', 'Parental Leave', 'Study Leave']),
            'code'              => strtoupper($this->faker->unique()->lexify('??')),
            'is_paid'           => true,
            'requires_approval' => true,
            'allow_half_day'    => true,
            'is_active'         => true,
            'min_notice_days'   => $this->faker->numberBetween(0, 14),
        ];
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => ['is_paid' => false]);
    }
}
