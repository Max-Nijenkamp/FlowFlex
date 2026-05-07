<?php

namespace Database\Factories\Crm;

use App\Enums\Crm\TicketPriority;
use App\Models\Company;
use App\Models\Crm\TicketSlaRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketSlaRule>
 */
class TicketSlaRuleFactory extends Factory
{
    protected $model = TicketSlaRule::class;

    public function definition(): array
    {
        return [
            'company_id'           => Company::factory(),
            'name'                 => $this->faker->words(3, true) . ' SLA',
            'priority'             => $this->faker->randomElement(TicketPriority::cases())->value,
            'first_response_hours' => $this->faker->numberBetween(1, 8),
            'resolution_hours'     => $this->faker->numberBetween(8, 72),
            'is_active'            => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
