<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\ProjectMilestone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectMilestone>
 */
class ProjectMilestoneFactory extends Factory
{
    protected $model = ProjectMilestone::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'project_id'  => Project::factory(),
            'name'        => fake()->words(3, true),
            'description' => fake()->sentence(),
            'due_date'    => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'sort_order'  => 0,
        ];
    }

    public function completed(): static
    {
        return $this->state(['completed_at' => now()]);
    }

    public function forProject(Project $project): static
    {
        return $this->state(['project_id' => $project->id, 'company_id' => $project->company_id]);
    }
}
