<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CannedResponse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CannedResponse>
 */
class CannedResponseFactory extends Factory
{
    protected $model = CannedResponse::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'title'      => $this->faker->sentence(3),
            'body'       => $this->faker->paragraph(),
            'is_shared'  => true,
        ];
    }

    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_shared' => false,
        ]);
    }
}
