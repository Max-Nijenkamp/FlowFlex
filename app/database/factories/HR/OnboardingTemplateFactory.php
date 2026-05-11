<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\OnboardingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingTemplate>
 */
class OnboardingTemplateFactory extends Factory
{
    protected $model = OnboardingTemplate::class;

    public function definition(): array
    {
        return [
            'name'        => fake()->randomElement(['Standard Onboarding', 'Tech Onboarding', 'Sales Onboarding', 'Remote Onboarding']),
            'description' => fake()->optional()->paragraph(),
            'is_active'   => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
