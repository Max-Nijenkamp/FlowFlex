<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
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
            'user_id'    => User::factory()->invited(),
            'company_id' => Company::factory(),
            'token'      => Str::random(64),
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state([
            'accepted_at' => null,
            'expires_at'  => now()->addDays(7),
        ]);
    }

    public function accepted(): static
    {
        return $this->state([
            'accepted_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state([
            'expires_at' => now()->subDay(),
        ]);
    }
}
