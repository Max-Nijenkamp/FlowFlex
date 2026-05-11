<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sprint>
 */
class SprintFactory extends Factory
{
    protected $model = Sprint::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'project_id' => Project::factory(),
            'name'       => 'Sprint ' . fake()->numberBetween(1, 20),
            'goal'       => fake()->sentence(),
            'start_date' => fake()->dateTimeBetween('-2 weeks', 'now')->format('Y-m-d'),
            'end_date'   => fake()->dateTimeBetween('now', '+2 weeks')->format('Y-m-d'),
            'status'     => 'planning',
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'velocity' => fake()->numberBetween(10, 50)]);
    }

    public function forProject(Project $project): static
    {
        return $this->state(['project_id' => $project->id, 'company_id' => $project->company_id]);
    }
}
