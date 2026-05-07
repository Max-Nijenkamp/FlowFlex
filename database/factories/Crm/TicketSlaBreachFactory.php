<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Ticket;
use App\Models\Crm\TicketSlaBreach;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketSlaBreach>
 */
class TicketSlaBreachFactory extends Factory
{
    protected $model = TicketSlaBreach::class;

    public function definition(): array
    {
        return [
            'company_id'  => Company::factory(),
            'ticket_id'   => Ticket::factory(),
            'type'        => $this->faker->randomElement(['first_response', 'resolution']),
            'breached_at' => $this->faker->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
