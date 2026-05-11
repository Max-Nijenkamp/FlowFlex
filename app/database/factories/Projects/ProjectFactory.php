<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'name'        => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status'      => fake()->randomElement(['planning', 'active', 'on_hold', 'completed', 'cancelled']),
            'priority'    => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'owner_id'    => User::factory(),
            'start_date'  => fake()->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'due_date'    => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'is_template' => false,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function planning(): static
    {
        return $this->state(['status' => 'planning']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed', 'completed_at' => now()]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
