<?php

namespace Database\Factories\Finance;

use App\Enums\Finance\InvoiceStatus;
use App\Models\Company;
use App\Models\Finance\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $subtotal  = $this->faker->randomFloat(2, 100, 10000);
        $taxAmount = round($subtotal * 0.21, 2);
        $total     = round($subtotal + $taxAmount, 2);

        return [
            'company_id'  => Company::factory(),
            'number'      => 'INV-' . $this->faker->unique()->numerify('####'),
            'currency'    => 'EUR',
            'issue_date'  => $this->faker->dateTimeBetween('-3 months', 'now'),
            'due_date'    => $this->faker->dateTimeBetween('now', '+2 months'),
            'status'      => $this->faker->randomElement(InvoiceStatus::cases())->value,
            'subtotal'    => $subtotal,
            'tax_amount'  => $taxAmount,
            'total'       => $total,
            'paid_amount' => 0,
            'tax_rate'    => 21.00,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Draft->value,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Sent->value,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => InvoiceStatus::Paid->value,
            'paid_amount' => $attributes['total'],
        ]);
    }
}
