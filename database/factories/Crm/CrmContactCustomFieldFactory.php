<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CrmContactCustomField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmContactCustomField>
 */
class CrmContactCustomFieldFactory extends Factory
{
    protected $model = CrmContactCustomField::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'name'        => $this->faker->words(2, true),
            'type'        => $this->faker->randomElement(['text', 'number', 'date', 'dropdown', 'checkbox']),
            'options'     => null,
            'is_required' => $this->faker->boolean(20),
            'sort_order'  => $this->faker->numberBetween(0, 50),
        ];
    }

    public function dropdown(): static
    {
        return $this->state(fn (array $attributes) => [
            'type'    => 'dropdown',
            'options' => ['Option A', 'Option B', 'Option C'],
        ]);
    }
}
