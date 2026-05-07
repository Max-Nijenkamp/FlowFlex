<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\ChatbotRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatbotRule>
 */
class ChatbotRuleFactory extends Factory
{
    protected $model = ChatbotRule::class;

    public function definition(): array
    {
        return [
            'company_id'       => Company::factory(),
            'name'             => $this->faker->words(3, true),
            'trigger_keywords' => $this->faker->words(3),
            'response_body'    => $this->faker->paragraph(),
            'is_active'        => true,
            'sort_order'       => $this->faker->numberBetween(0, 100),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
