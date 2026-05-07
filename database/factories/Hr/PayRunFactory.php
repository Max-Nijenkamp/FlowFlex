<?php

namespace Database\Factories\Hr;

use App\Enums\Hr\PayFrequency;
use App\Enums\Hr\PayRunStatus;
use App\Models\Company;
use App\Models\Hr\PayRun;
use App\Models\Hr\PayrollEntity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayRun>
 */
class PayRunFactory extends Factory
{
    protected $model = PayRun::class;

    public function definition(): array
    {
        $start = now()->startOfMonth();

        return [
            'company_id'        => Company::factory(),
            'payroll_entity_id' => PayrollEntity::factory(),
            'status'            => PayRunStatus::Draft->value,
            'pay_frequency'     => PayFrequency::Monthly->value,
            'pay_period_start'  => $start->format('Y-m-d'),
            'pay_period_end'    => $start->copy()->endOfMonth()->format('Y-m-d'),
            'payment_date'      => $start->copy()->endOfMonth()->format('Y-m-d'),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => PayRunStatus::Approved->value,
            'approved_at' => now(),
        ]);
    }

    public function processed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => PayRunStatus::Processed->value,
            'processed_at' => now(),
        ]);
    }
}
