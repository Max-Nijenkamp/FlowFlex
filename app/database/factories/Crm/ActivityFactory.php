<?php

declare(strict_types=1);

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\Activity;
use App\Models\Crm\Contact;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Activity> */
class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'type' => 'call',
            'subject' => fake()->sentence(4),
            'owner_id' => User::factory(),
            'contact_id' => Contact::factory(),
            'activity_date' => now(),
            'is_complete' => true,
        ];
    }

    public function task(): static
    {
        return $this->state([
            'type' => 'task',
            'is_complete' => false,
            'due_at' => now()->addHours(6),
        ]);
    }
}
