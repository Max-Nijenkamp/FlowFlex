<?php

declare(strict_types=1);

namespace Database\Factories\CRM;

use App\Models\Company;
use App\Models\CRM\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('+316########'),
            'source' => fake()->randomElement(['manual', 'website', 'referral', 'event']),
            'status' => fake()->randomElement(['new', 'working', 'qualified']),
            'estimated_value_cents' => fake()->numberBetween(50_000, 5_000_000),
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }
}
