<?php

namespace Database\Factories\Projects;

use App\Models\Company;
use App\Models\Projects\TaskLabel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaskLabel>
 */
class TaskLabelFactory extends Factory
{
    protected $model = TaskLabel::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name'       => $this->faker->randomElement(['Bug', 'Feature', 'Enhancement', 'Documentation', 'Review', 'Blocked']),
            'color'      => $this->faker->hexColor(),
        ];
    }
}
