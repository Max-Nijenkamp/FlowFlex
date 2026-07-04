<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\ModuleCatalogEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ModuleCatalogEntry> */
class ModuleCatalogEntryFactory extends Factory
{
    protected $model = ModuleCatalogEntry::class;

    public function definition(): array
    {
        return [
            'module_key' => 'test.'.fake()->unique()->slug(2),
            'domain' => 'test',
            'name' => fake()->words(2, true),
            'per_user_monthly_price' => fake()->randomElement([0, 300, 400, 500]),
            'is_active' => true,
        ];
    }

    public function free(): static
    {
        return $this->state(['per_user_monthly_price' => 0]);
    }
}
