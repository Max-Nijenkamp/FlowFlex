<?php

declare(strict_types=1);

namespace Database\Factories\HR;

use App\Models\HR\PayrollEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PayrollEntry>
 */
class PayrollEntryFactory extends Factory
{
    protected $model = PayrollEntry::class;

    public function definition(): array
    {
        $gross = fake()->randomFloat(2, 2000, 10000);
        $net   = round($gross * 0.72, 2);

        return [
            'gross_pay'  => $gross,
            'net_pay'    => $net,
            'deductions' => [
                ['type' => 'tax', 'amount' => round($gross * 0.20, 2)],
                ['type' => 'pension', 'amount' => round($gross * 0.08, 2)],
            ],
            'additions'  => [],
            'notes'      => null,
        ];
    }
}
