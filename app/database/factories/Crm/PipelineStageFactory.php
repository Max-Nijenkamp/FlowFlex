<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Pipeline;
use App\Models\Crm\PipelineStage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<PipelineStage> */
class PipelineStageFactory extends Factory
{
    protected $model = PipelineStage::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'pipeline_id' => Pipeline::factory(),
            'name' => fake()->unique()->word(),
            'order' => fake()->numberBetween(1, 20),
            'probability_default' => 20,
            'is_won' => false,
            'is_lost' => false,
        ];
    }
}
