<?php

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\OnboardingTemplate;
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
            'company_id'  => Company::factory(),
            'name'        => $this->faker->randomElement(['Standard Onboarding', 'Engineering Onboarding', 'Remote Onboarding']),
            'description' => $this->faker->sentence(),
            'is_active'   => true,
        ];
    }
}
