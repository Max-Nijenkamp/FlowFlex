<?php

declare(strict_types=1);

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
        $name = fake()->company();

        return [
            'name'     => $name,
            'slug'     => Str::slug($name) . '-' . Str::lower(Str::random(4)),
            'email'    => fake()->companyEmail(),
            'status'   => 'trial',
            'timezone' => 'UTC',
            'locale'   => 'en',
            'currency' => 'EUR',
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function suspended(): static
    {
        return $this->state(['status' => 'suspended']);
    }
}
