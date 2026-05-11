<?php

declare(strict_types=1);

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\Task;
use App\Models\Projects\TaskComment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskComment>
 */
class TaskCommentFactory extends Factory
{
    protected $model = TaskComment::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'task_id'    => Task::factory(),
            'user_id'    => User::factory(),
            'body'       => fake()->paragraph(),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
