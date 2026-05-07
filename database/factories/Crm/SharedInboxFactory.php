<?php

namespace Database\Factories\Crm;

use App\Models\Company;
use App\Models\Crm\SharedInbox;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SharedInbox>
 */
class SharedInboxFactory extends Factory
{
    protected $model = SharedInbox::class;

    public function definition(): array
    {
        return [
            'company_id'    => Company::factory(),
            'name'          => $this->faker->words(2, true) . ' Inbox',
            'email_address' => $this->faker->unique()->safeEmail(),
            'is_active'     => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
