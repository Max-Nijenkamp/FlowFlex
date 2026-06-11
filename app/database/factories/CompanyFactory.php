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
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(5)),
            'subscription_status' => 'active',
            'timezone' => 'Europe/Amsterdam',
            'locale' => 'en',
            'currency' => 'EUR',
            'trial_ends_at' => null,
            'setup_completed_at' => now(),
        ];
    }

    public function trial(): static
    {
        return $this->state(fn () => [
            'subscription_status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
            'setup_completed_at' => null,
        ]);
    }
}
