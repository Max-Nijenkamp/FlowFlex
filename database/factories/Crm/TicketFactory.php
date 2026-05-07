<?php

namespace Database\Factories\Crm;

use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Company;
use App\Models\Crm\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'subject'    => $this->faker->sentence(4),
            'status'     => TicketStatus::Open->value,
            'priority'   => $this->faker->randomElement(TicketPriority::cases())->value,
        ];
    }

    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => TicketStatus::Resolved->value,
            'resolved_at' => now(),
        ]);
    }

    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::High->value,
        ]);
    }
}
