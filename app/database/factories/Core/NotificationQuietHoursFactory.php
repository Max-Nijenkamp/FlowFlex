<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Core\NotificationQuietHours;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationQuietHours>
 */
class NotificationQuietHoursFactory extends Factory
{
    protected $model = NotificationQuietHours::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'start_time'   => '22:00:00',
            'end_time'     => '07:00:00',
            'timezone'     => 'Europe/Amsterdam',
            'days_of_week' => null,
        ];
    }

    public function weekdaysOnly(): static
    {
        return $this->state(['days_of_week' => [1, 2, 3, 4, 5]]);
    }
}
