<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CompanyModuleSubscription> */
class CompanyModuleSubscriptionFactory extends Factory
{
    protected $model = CompanyModuleSubscription::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'module_key' => 'test.'.fake()->unique()->slug(2),
            'activated_at' => now(),
            'deactivated_at' => null,
            'activated_by' => null,
        ];
    }
}
