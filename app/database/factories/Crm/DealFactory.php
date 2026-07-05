<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Deal;
use App\Models\Crm\PipelineStage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Deal> */
class DealFactory extends Factory
{
    protected $model = Deal::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => fake()->company().' deal',
            'owner_id' => User::factory(),
            'stage_id' => PipelineStage::factory(),
            'value_cents' => fake()->numberBetween(50_000, 5_000_000),
            'currency' => 'EUR',
            'probability' => 20,
            'stage_entered_at' => now(),
        ];
    }
}
