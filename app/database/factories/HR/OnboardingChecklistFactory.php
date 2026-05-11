<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\Employee;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingChecklist>
 */
class OnboardingChecklistFactory extends Factory
{
    protected $model = OnboardingChecklist::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'employee_id' => Employee::factory(),
            'template_id' => OnboardingTemplate::factory(),
            'start_date'  => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'completed_at' => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(['completed_at' => now()]);
    }
}
