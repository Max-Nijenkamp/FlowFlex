<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Company> */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'subscription_status' => 'trial',
            'timezone' => 'Europe/Amsterdam',
            'locale' => 'en',
            'currency' => 'EUR',
            'trial_ends_at' => now()->addDays(14),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'subscription_status' => 'active',
            'trial_ends_at' => null,
        ]);
    }
}
