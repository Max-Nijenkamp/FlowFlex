<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\Projects\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'project_id'     => Project::factory(),
            'title'          => fake()->sentence(4),
            'description'    => fake()->paragraph(),
            'created_by'     => User::factory(),
            'status'         => fake()->randomElement(['todo', 'in_progress', 'in_review', 'done', 'cancelled']),
            'priority'       => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'due_date'       => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'estimate_hours' => fake()->randomFloat(1, 0.5, 40),
            'story_points'   => fake()->numberBetween(1, 13),
            'sort_order'     => 0,
        ];
    }

    public function todo(): static
    {
        return $this->state(['status' => 'todo']);
    }

    public function done(): static
    {
        return $this->state(['status' => 'done', 'completed_at' => now()]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }

    public function forProject(Project $project): static
    {
        return $this->state(['project_id' => $project->id, 'company_id' => $project->company_id]);
    }
}
