<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\NotificationLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationLog>
 */
class NotificationLogFactory extends Factory
{
    protected $model = NotificationLog::class;

    public function definition(): array
    {
        $company = Company::factory()->create();

        return [
            'company_id' => $company->id,
            'user_id'    => User::factory()->create(['company_id' => $company->id])->id,
            'event_type' => fake()->randomElement(['hr.leave.approved', 'hr.leave.rejected', 'user.invited']),
            'channel'    => fake()->randomElement(['database', 'mail']),
            'status'     => 'sent',
            'payload'    => null,
            'sent_at'    => now(),
            'read_at'    => null,
        ];
    }

    public function read(): static
    {
        return $this->state(['read_at' => now(), 'status' => 'read']);
    }

    public function failed(): static
    {
        return $this->state(['status' => 'failed']);
    }
}
