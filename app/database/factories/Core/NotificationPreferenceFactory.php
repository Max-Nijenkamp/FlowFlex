<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\NotificationPreference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationPreference>
 */
class NotificationPreferenceFactory extends Factory
{
    protected $model = NotificationPreference::class;

    public function definition(): array
    {
        $company = Company::factory()->create();

        return [
            'user_id'       => User::factory()->create(['company_id' => $company->id])->id,
            'company_id'    => $company->id,
            'event_type'    => fake()->randomElement(['hr.leave.approved', 'hr.leave.rejected', 'user.invited']),
            'channel'       => fake()->randomElement(['database', 'mail']),
            'enabled'       => true,
            'delivery_mode' => 'realtime',
            'digest_time'   => null,
            'timezone'      => null,
        ];
    }

    public function disabled(): static
    {
        return $this->state(['enabled' => false]);
    }

    public function digest(): static
    {
        return $this->state([
            'delivery_mode' => 'digest',
            'digest_time'   => '08:00:00',
        ]);
    }
}
