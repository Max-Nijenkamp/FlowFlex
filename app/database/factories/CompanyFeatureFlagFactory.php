<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyFeatureFlag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyFeatureFlag>
 */
class CompanyFeatureFlagFactory extends Factory
{
    protected $model = CompanyFeatureFlag::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'flag'       => fake()->unique()->slug(2),
            'enabled'    => true,
        ];
    }

    public function disabled(): static
    {
        return $this->state(['enabled' => false]);
    }

    public function global(): static
    {
        return $this->state(['company_id' => null]);
    }
}
