<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\LeaveRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate   = (clone $startDate)->modify('+' . fake()->numberBetween(1, 10) . ' days');

        return [
            'start_date'     => $startDate->format('Y-m-d'),
            'end_date'       => $endDate->format('Y-m-d'),
            'days_requested' => fake()->randomFloat(1, 1, 10),
            'reason'         => fake()->optional()->sentence(),
            'status'         => 'pending',
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state([
            'status'           => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
