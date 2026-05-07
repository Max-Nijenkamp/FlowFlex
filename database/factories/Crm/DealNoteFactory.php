<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Deal;
use App\Models\Crm\DealNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DealNote>
 */
class DealNoteFactory extends Factory
{
    protected $model = DealNote::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'deal_id'    => Deal::factory(),
            'body'       => $this->faker->paragraph(),
        ];
    }
}
