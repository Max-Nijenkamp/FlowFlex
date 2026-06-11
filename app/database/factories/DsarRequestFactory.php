<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\DsarRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DsarRequest>
 */
class DsarRequestFactory extends Factory
{
    protected $model = DsarRequest::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'subject_email' => fake()->safeEmail(),
            'request_type' => 'access',
            'due_at' => now()->addDays(30),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }

    public function erasure(): static
    {
        return $this->state(fn () => ['request_type' => 'erasure']);
    }
}
