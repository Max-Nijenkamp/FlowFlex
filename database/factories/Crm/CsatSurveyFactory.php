<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\CsatSurvey;
use App\Models\Crm\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<CsatSurvey>
 */
class CsatSurveyFactory extends Factory
{
    protected $model = CsatSurvey::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'ticket_id'  => Ticket::factory(),
            'token'      => Str::random(64),
            'sent_at'    => null,
            'expires_at' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent_at'    => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }
}
