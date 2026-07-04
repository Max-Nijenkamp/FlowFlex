<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\BillingServiceInterface;
use App\Models\Company;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

/**
 * Monthly 1st 01:00 (core.billing-engine/monthly-invoicing). Idempotent:
 * the unique (company_id, period_start) makes re-runs skip existing.
 */
class GenerateMonthlyInvoicesCommand extends Command
{
    protected $signature = 'billing:generate-invoices {--period= : YYYY-MM, defaults to last month}';

    protected $description = 'Generate the monthly usage invoice for every company';

    public function handle(BillingServiceInterface $billing): int
    {
        $period = $this->option('period') !== null
            ? CarbonImmutable::createFromFormat('Y-m', (string) $this->option('period'))
            : CarbonImmutable::now()->subMonth();

        $generated = 0;

        Company::query()->eachById(function (Company $company) use ($billing, $period, &$generated): void {
            if ($billing->generateMonthlyInvoice($company->id, $period) !== null) {
                $generated++;
            }
        });

        $this->info("Generated {$generated} invoices for {$period->format('F Y')}.");

        return self::SUCCESS;
    }
}
