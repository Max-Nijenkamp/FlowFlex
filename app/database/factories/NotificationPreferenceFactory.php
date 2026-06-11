<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\NotificationPreference;
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
        return [
            'company_id' => Company::factory(),
            'user_id' => User::factory(),
            'notification_type' => 'test.notification',
            'in_app_enabled' => true,
            'email_enabled' => true,
        ];
    }
}
