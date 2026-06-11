<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingPlan;
use App\Models\HR\OnboardingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingPlan>
 */
class OnboardingPlanFactory extends Factory
{
    protected $model = OnboardingPlan::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'employee_id' => Employee::factory(),
            'template_id' => OnboardingTemplate::factory(),
            'started_at' => now(),
        ];
    }
}
