<?php

declare(strict_types=1);

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\OnboardingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<OnboardingTemplate> */
class OnboardingTemplateFactory extends Factory
{
    protected $model = OnboardingTemplate::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Standard onboarding',
            'is_default' => true,
        ];
    }
}
