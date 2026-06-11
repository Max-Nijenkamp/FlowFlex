<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\WebhookEndpoint;
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
            'company_id' => Company::factory(),
            'url' => 'https://example.com/hooks/flowflex',
            'secret' => 'whsec_'.Str::random(40),
            'events' => ['ModuleActivated'],
            'is_active' => true,
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }

    /** @param list<string> $events */
    public function events(array $events): static
    {
        return $this->state(fn () => ['events' => $events]);
    }
}
