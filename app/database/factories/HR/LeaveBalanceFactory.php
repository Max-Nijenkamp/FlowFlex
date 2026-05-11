<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\LeaveBalance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
{
    protected $model = LeaveBalance::class;

    public function definition(): array
    {
        return [
            'year'           => now()->year,
            'allocated_days' => fake()->randomFloat(1, 10, 30),
            'used_days'      => 0,
            'pending_days'   => 0,
        ];
    }
}
