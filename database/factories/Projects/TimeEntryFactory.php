<?php

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Task;
use App\Models\Projects\TimeEntry;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $end   = (clone $start)->modify('+' . $this->faker->numberBetween(30, 480) . ' minutes');

        return [
            'company_id'       => Company::factory(),
            'tenant_id'        => Tenant::factory(),
            'task_id'          => Task::factory(),
            'description'      => $this->faker->sentence(),
            'started_at'       => $start,
            'ended_at'         => $end,
            'duration_minutes' => (int) (($end->getTimestamp() - $start->getTimestamp()) / 60),
            'is_billable'      => $this->faker->boolean(70),
            'is_approved'      => false,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => ['is_approved' => true]);
    }
}
