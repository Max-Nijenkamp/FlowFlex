<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name'       => $name,
            'slug'       => Str::slug($name) . '-' . Str::random(6),
            'email'      => $this->faker->unique()->companyEmail(),
            'phone'      => $this->faker->optional()->phoneNumber(),
            'timezone'   => 'UTC',
            'is_enabled' => true,
            'settings'   => [],
        ];
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }
}
