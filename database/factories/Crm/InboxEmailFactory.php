<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\InboxEmail;
use App\Models\Crm\SharedInbox;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<InboxEmail>
 */
class InboxEmailFactory extends Factory
{
    protected $model = InboxEmail::class;

    public function definition(): array
    {
        return [
            'company_id'      => Company::factory(),
            'shared_inbox_id' => SharedInbox::factory(),
            'message_id'      => '<' . Str::random(20) . '@mail.example.com>',
            'from_email'      => $this->faker->safeEmail(),
            'from_name'       => $this->faker->name(),
            'subject'         => $this->faker->sentence(6),
            'body_html'       => '<p>' . $this->faker->paragraph() . '</p>',
            'body_text'       => $this->faker->paragraph(),
            'status'          => 'unread',
            'received_at'     => $this->faker->dateTimeBetween('-7 days', 'now'),
        ];
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'read',
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
