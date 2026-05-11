<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\PayrollRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollRun>
 */
class PayrollRunFactory extends Factory
{
    protected $model = PayrollRun::class;

    public function definition(): array
    {
        $periodStart = fake()->dateTimeBetween('-6 months', 'now');
        $periodEnd   = (clone $periodStart)->modify('+1 month -1 day');
        $payDate     = (clone $periodEnd)->modify('+5 days');

        return [
            'name'         => $periodStart->format('F Y') . ' Payroll',
            'period_start' => $periodStart->format('Y-m-d'),
            'period_end'   => $periodEnd->format('Y-m-d'),
            'pay_date'     => $payDate->format('Y-m-d'),
            'status'       => 'draft',
            'total_gross'  => 0,
            'total_net'    => 0,
            'total_deductions' => 0,
            'currency'     => 'EUR',
        ];
    }

    public function approved(): static
    {
        return $this->state([
            'status'      => 'approved',
            'approved_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state(['status' => 'draft']);
    }
}
