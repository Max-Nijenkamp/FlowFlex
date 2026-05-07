<?php

namespace Database\Factories\Hr;

use App\Models\Company;
use App\Models\Hr\PayrollEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollEntity>
 */
class PayrollEntityFactory extends Factory
{
    protected $model = PayrollEntity::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'name'        => $this->faker->company() . ' Payroll',
            'legal_name'  => $this->faker->company() . ' BV',
            'country_code'=> 'NL',
            'is_default'  => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => ['is_default' => true]);
    }
}
