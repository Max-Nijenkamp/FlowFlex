<?php

namespace Database\Factories\Hr;

use App\Enums\Hr\OnboardingFlowStatus;
use App\Models\Company;
use App\Models\Hr\Employee;
use App\Models\Hr\OnboardingFlow;
use App\Models\Hr\OnboardingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingFlow>
 */
class OnboardingFlowFactory extends Factory
{
    protected $model = OnboardingFlow::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'employee_id' => Employee::factory(),
            'template_id' => OnboardingTemplate::factory(),
            'status'      => OnboardingFlowStatus::InProgress->value,
            'started_at'  => now(),
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => OnboardingFlowStatus::Completed->value,
            'completed_at' => now(),
        ]);
    }
}
