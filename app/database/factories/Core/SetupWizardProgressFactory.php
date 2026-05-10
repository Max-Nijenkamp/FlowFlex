<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\SetupWizardProgress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SetupWizardProgress>
 */
class SetupWizardProgressFactory extends Factory
{
    protected $model = SetupWizardProgress::class;

    public function definition(): array
    {
        return [
            'company_id'      => Company::factory(),
            'completed_steps' => [],
            'current_step'    => 'welcome',
            'completed'       => false,
            'completed_at'    => null,
        ];
    }

    public function completed(): static
    {
        return $this->state([
            'completed_steps' => SetupWizardProgress::steps(),
            'current_step'    => 'done',
            'completed'       => true,
            'completed_at'    => now(),
        ]);
    }

    public function atStep(string $step): static
    {
        $steps = SetupWizardProgress::steps();
        $stepIndex = array_search($step, $steps, true);
        $completedSteps = $stepIndex > 0 ? array_slice($steps, 0, $stepIndex) : [];

        return $this->state([
            'completed_steps' => $completedSteps,
            'current_step'    => $step,
        ]);
    }
}
