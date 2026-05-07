<?php

namespace Database\Factories\Projects;

use App\Enums\Projects\TimesheetStatus;
use App\Models\Company;
use App\Models\Projects\Timesheet;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Timesheet>
 */
class TimesheetFactory extends Factory
{
    protected $model = Timesheet::class;

    public function definition(): array
    {
        $weekStart = now()->startOfWeek();

        return [
            'company_id'  => Company::factory(),
            'tenant_id'   => Tenant::factory(),
            'week_start'  => $weekStart->format('Y-m-d'),
            'week_end'    => $weekStart->copy()->endOfWeek()->format('Y-m-d'),
            'status'      => TimesheetStatus::Draft->value,
            'total_hours' => 0,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => TimesheetStatus::Submitted->value,
            'submitted_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TimesheetStatus::Approved->value,
        ]);
    }
}
