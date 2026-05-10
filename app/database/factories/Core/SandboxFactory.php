<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\Sandbox;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sandbox>
 */
class SandboxFactory extends Factory
{
    protected $model = Sandbox::class;

    public function definition(): array
    {
        return [
            'company_id'     => Company::factory(),
            'status'         => 'active',
            'database_name'  => 'sandbox_' . fake()->unique()->lexify('??????????'),
            'seed_type'      => 'empty',
            'provisioned_at' => now(),
            'last_synced_at' => null,
        ];
    }

    public function provisioning(): static
    {
        return $this->state([
            'status'         => 'provisioning',
            'provisioned_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(['status' => 'suspended']);
    }
}
