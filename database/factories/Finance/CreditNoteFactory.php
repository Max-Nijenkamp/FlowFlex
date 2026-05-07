<?php

namespace Database\Factories\Finance;

use App\Models\Company;
use App\Models\Finance\CreditNote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CreditNote>
 */
class CreditNoteFactory extends Factory
{
    protected $model = CreditNote::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'number'     => 'CN-' . $this->faker->unique()->numerify('####'),
            'amount'     => $this->faker->randomFloat(2, 10, 5000),
            'reason'     => $this->faker->optional()->sentence(),
            'issued_at'  => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }

    public function forInvoice(string $invoiceId): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_id' => $invoiceId,
        ]);
    }
}
