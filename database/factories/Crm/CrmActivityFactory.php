<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CrmActivity;
use App\Models\Crm\CrmContact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CrmActivity>
 */
class CrmActivityFactory extends Factory
{
    protected $model = CrmActivity::class;

    public function definition(): array
    {
        return [
            'company_id'   => Company::factory(),
            'subject_type' => CrmContact::class,
            'subject_id'   => CrmContact::factory(),
            'type'         => $this->faker->randomElement(['note', 'call', 'email', 'meeting', 'deal_update', 'ticket_update']),
            'description'  => $this->faker->sentence(),
            'metadata'     => null,
            'occurred_at'  => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
