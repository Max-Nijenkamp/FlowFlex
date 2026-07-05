<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Pipeline;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Pipeline> */
class PipelineFactory extends Factory
{
    protected $model = Pipeline::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Sales pipeline',
            'is_default' => true,
        ];
    }
}
