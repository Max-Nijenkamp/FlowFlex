<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Deal;
use App\Models\Crm\DealProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<DealProduct> */
class DealProductFactory extends Factory
{
    protected $model = DealProduct::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'deal_id' => Deal::factory(),
            'description' => fake()->words(3, true),
            'quantity' => 1,
            'unit_price_cents' => fake()->numberBetween(1_000, 100_000),
            'discount_percent' => 0,
        ];
    }
}
