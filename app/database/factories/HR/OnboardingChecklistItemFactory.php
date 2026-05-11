<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\Company;
use App\Models\HR\OnboardingChecklist;
use App\Models\HR\OnboardingChecklistItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OnboardingChecklistItem>
 */
class OnboardingChecklistItemFactory extends Factory
{
    protected $model = OnboardingChecklistItem::class;

    public function definition(): array
    {
        return [
            'company_id'   => Company::factory(),
            'checklist_id' => OnboardingChecklist::factory(),
            'title'        => $this->faker->sentence(4),
            'description'  => $this->faker->optional()->paragraph(),
            'is_required'  => $this->faker->boolean(80),
            'sort_order'   => $this->faker->numberBetween(0, 100),
            'due_date'     => $this->faker->optional()->dateTimeBetween('now', '+30 days')?->format('Y-m-d'),
            'completed_at' => null,
            'assignee_id'  => null,
        ];
    }

    public function completed(): static
    {
        return $this->state(['completed_at' => now()]);
    }
}
