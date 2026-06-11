<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\UserInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserInvitation>
 */
class UserInvitationFactory extends Factory
{
    protected $model = UserInvitation::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'email' => fake()->unique()->safeEmail(),
            'token' => (string) Str::uuid(),
            'role' => 'employee',
            'invited_by' => User::factory(),
            'expires_at' => now()->addDays(7),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => [
            'company_id' => $company->id,
            'invited_by' => User::factory()->forCompany($company),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => ['expires_at' => now()->subDay()]);
    }

    public function revoked(): static
    {
        return $this->state(fn () => ['revoked_at' => now()]);
    }
}
