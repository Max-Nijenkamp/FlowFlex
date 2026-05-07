<?php

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\MileageRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MileageRate>
 */
class MileageRateFactory extends Factory
{
    protected $model = MileageRate::class;

    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'name'           => $this->faker->words(2, true) . ' Rate',
            'rate_per_km'    => $this->faker->randomFloat(4, 0.10, 0.60),
            'currency'       => 'EUR',
            'effective_from' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'effective_to'   => null,
            'is_active'      => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active'  => false,
            'effective_to' => now(),
        ]);
    }
}
