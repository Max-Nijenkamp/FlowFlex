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
            'company_id' => Company::factory(),
            'period_start' => now()->subMonth()->startOfMonth(),
            'period_end' => now()->subMonth()->endOfMonth(),
            'total_cents' => 0,
            'currency' => 'EUR',
        ];
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn () => ['company_id' => $company->id]);
    }
}
