<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\OnboardingTemplate;
use App\Models\HR\OnboardingTemplateTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingTemplateTask>
 */
class OnboardingTemplateTaskFactory extends Factory
{
    protected $model = OnboardingTemplateTask::class;

    public function definition(): array
    {
        return [
            'company_id'          => Company::factory(),
            'template_id'         => OnboardingTemplate::factory(),
            'title'               => $this->faker->sentence(4),
            'description'         => $this->faker->optional()->paragraph(),
            'is_required'         => $this->faker->boolean(80),
            'sort_order'          => $this->faker->numberBetween(0, 100),
            'due_days_after_hire' => $this->faker->numberBetween(1, 30),
            'assignee_role'       => null,
        ];
    }
}
