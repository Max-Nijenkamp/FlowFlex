<?php

namespace Database\Factories\Hr;

use App\Enums\Hr\LeaveRequestStatus;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('now', '+3 months');
        $end   = $this->faker->dateTimeBetween($start, '+1 month');

        return [
            'company_id'    => Company::factory(),
            'employee_id'   => Employee::factory(),
            'leave_type_id' => LeaveType::factory(),
            'start_date'    => $start->format('Y-m-d'),
            'end_date'      => $end->format('Y-m-d'),
            'total_days'    => $this->faker->numberBetween(1, 14),
            'is_half_day'   => false,
            'status'        => LeaveRequestStatus::Pending->value,
            'reason'        => $this->faker->sentence(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => LeaveRequestStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'           => LeaveRequestStatus::Rejected->value,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
