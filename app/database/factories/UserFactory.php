<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'company_id'         => Company::factory(),
            'first_name'         => fake()->firstName(),
            'last_name'          => fake()->lastName(),
            'email'              => fake()->unique()->safeEmail(),
            'password'           => bcrypt('password'),
            'locale'             => 'en',
            'timezone'           => 'UTC',
            'status'             => 'active',
            'two_factor_enabled' => false,
            'email_verified_at'  => now(),
        ];
    }

    public function invited(): static
    {
        return $this->state(['status' => 'invited', 'email_verified_at' => null]);
    }

    public function deactivated(): static
    {
        return $this->state(['status' => 'deactivated']);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(['company_id' => $company->id]);
    }
}
