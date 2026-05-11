<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Task;
use App\Models\Projects\TaskDependency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskDependency>
 */
class TaskDependencyFactory extends Factory
{
    protected $model = TaskDependency::class;

    public function definition(): array
    {
        return [
            'company_id'        => Company::factory(),
            'task_id'           => Task::factory(),
            'depends_on_task_id' => Task::factory(),
            'dependency_type'   => 'finish_to_start',
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
