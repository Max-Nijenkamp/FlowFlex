<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Core\NotificationWatch;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NotificationWatch>
 */
class NotificationWatchFactory extends Factory
{
    protected $model = NotificationWatch::class;

    public function definition(): array
    {
        return [
            'user_id'        => User::factory(),
            'watchable_type' => 'App\\Models\\User',
            'watchable_id'   => User::factory(),
        ];
    }
}
