<?php

namespace Database\Factories\Projects;

use App\Enums\Projects\TaskPriority;
use App\Enums\Projects\TaskStatus;
use App\Models\Company;
use App\Models\Projects\Task;
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
            'company_id' => Company::factory(),
            'title'      => $this->faker->sentence(4),
            'description'=> $this->faker->paragraph(),
            'priority'   => $this->faker->randomElement(TaskPriority::cases())->value,
            'status'     => TaskStatus::Todo->value,
            'due_date'   => $this->faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
        ];
    }

    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Done->value,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::InProgress->value,
        ]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attributes) => [
            'company_id' => $company->id,
        ]);
    }
}
