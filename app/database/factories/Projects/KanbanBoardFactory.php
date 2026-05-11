<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\KanbanBoard;
use App\Models\Projects\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KanbanBoard>
 */
class KanbanBoardFactory extends Factory
{
    protected $model = KanbanBoard::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'project_id'  => Project::factory(),
            'name'        => fake()->words(2, true) . ' Board',
            'description' => fake()->sentence(),
            'is_default'  => false,
        ];
    }

    public function default(): static
    {
        return $this->state(['is_default' => true]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
