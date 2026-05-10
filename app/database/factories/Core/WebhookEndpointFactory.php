<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\WebhookEndpoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WebhookEndpoint>
 */
class WebhookEndpointFactory extends Factory
{
    protected $model = WebhookEndpoint::class;

    public function definition(): array
    {
        return [
            'company_id'        => Company::factory(),
            'url'               => fake()->url(),
            'events'            => ['user.created', 'user.updated'],
            'secret'            => Str::random(32),
            'is_active'         => true,
            'failure_count'     => 0,
            'last_triggered_at' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
