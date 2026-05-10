<?php

declare(strict_types=1);

namespace Database\Factories\Core;

use App\Models\Company;
use App\Models\Core\BillingInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillingInvoice>
 */
class BillingInvoiceFactory extends Factory
{
    protected $model = BillingInvoice::class;

    public function definition(): array
    {
        return [
            'company_id'       => Company::factory(),
            'stripe_invoice_id' => 'in_' . fake()->unique()->regexify('[A-Za-z0-9]{14}'),
            'amount'           => fake()->randomFloat(2, 5, 500),
            'currency'         => 'EUR',
            'status'           => 'open',
            'invoice_pdf_url'  => null,
            'due_date'         => now()->addDays(30),
            'paid_at'          => null,
        ];
    }

    public function paid(): static
    {
        return $this->state([
            'status'  => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state([
            'status'           => 'draft',
            'stripe_invoice_id' => null,
        ]);
    }
}
