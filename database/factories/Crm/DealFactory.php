<?php

namespace Database\Factories\Crm;

use App\Enums\Crm\DealStatus;
use App\Models\Company;
use App\Models\Crm\Deal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Deal>
 */
class DealFactory extends Factory
{
    protected $model = Deal::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title'      => $this->faker->sentence(3),
            'value'      => $this->faker->optional()->randomFloat(2, 500, 100000),
            'currency'   => 'EUR',
            'status'     => DealStatus::Open->value,
            'close_probability' => $this->faker->numberBetween(10, 90),
            'expected_close_date' => $this->faker->optional()->dateTimeBetween('now', '+6 months'),
        ];
    }

    public function won(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'    => DealStatus::Won->value,
            'closed_at' => now(),
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => DealStatus::Lost->value,
            'closed_at'   => now(),
            'lost_reason' => $this->faker->sentence(),
        ]);
    }
}
