<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Core\ApiClient;
use App\Models\Core\ApiToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ApiToken>
 */
class ApiTokenFactory extends Factory
{
    protected $model = ApiToken::class;

    public function definition(): array
    {
        return [
            'api_client_id' => ApiClient::factory(),
            'token'         => Str::random(64),
            'scopes'        => ['read'],
            'expires_at'    => now()->addYear(),
            'last_used_at'  => null,
        ];
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }

    public function nonExpiring(): static
    {
        return $this->state(['expires_at' => null]);
    }
}
