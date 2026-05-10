<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\ApiClient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ApiClient>
 */
class ApiClientFactory extends Factory
{
    protected $model = ApiClient::class;

    public function definition(): array
    {
        $company = Company::factory()->create();

        return [
            'company_id'    => $company->id,
            'created_by'    => User::factory()->create(['company_id' => $company->id])->id,
            'name'          => fake()->words(3, true) . ' Integration',
            'client_id'     => Str::random(40),
            'client_secret' => Str::random(40),
            'scopes'        => ['read'],
            'allowed_ips'   => null,
            'is_active'     => true,
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn (array $attr) => [
            'company_id' => $company->id,
            'created_by' => User::factory()->create(['company_id' => $company->id])->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
