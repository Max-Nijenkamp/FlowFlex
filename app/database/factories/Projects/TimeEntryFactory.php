<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Models\Projects\TimeEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'user_id'     => User::factory(),
            'project_id'  => Project::factory(),
            'date'        => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'hours'       => fake()->randomFloat(2, 0.25, 8),
            'description' => fake()->sentence(),
            'is_billable' => fake()->boolean(),
        ];
    }

    public function billable(): static
    {
        return $this->state(['is_billable' => true, 'billing_rate' => fake()->randomFloat(2, 50, 200)]);
    }

    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'approved_by' => $attributes['user_id'],
                'approved_at' => now(),
            ];
        });
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
