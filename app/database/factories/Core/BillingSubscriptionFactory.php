<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\BillingSubscription;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillingSubscription>
 */
class BillingSubscriptionFactory extends Factory
{
    protected $model = BillingSubscription::class;

    public function definition(): array
    {
        return [
            'company_id'             => Company::factory(),
            'stripe_customer_id'     => 'cus_' . fake()->regexify('[A-Za-z0-9]{14}'),
            'stripe_subscription_id' => null,
            'status'                 => 'trialing',
            'user_count'             => 1,
            'monthly_amount'         => 0.00,
            'currency'               => 'EUR',
            'trial_ends_at'          => now()->addDays(14),
            'current_period_start'   => now(),
            'current_period_end'     => now()->addMonth(),
            'canceled_at'            => null,
        ];
    }

    public function active(): static
    {
        return $this->state([
            'status'                 => 'active',
            'stripe_subscription_id' => 'sub_' . fake()->regexify('[A-Za-z0-9]{14}'),
            'trial_ends_at'          => null,
        ]);
    }

    public function canceled(): static
    {
        return $this->state([
            'status'      => 'canceled',
            'canceled_at' => now(),
        ]);
    }

    public function pastDue(): static
    {
        return $this->state(['status' => 'past_due']);
    }
}
