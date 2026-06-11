<?php

declare(strict_types=1);

namespace App\Console\Commands\Core;

use App\Contracts\Core\BillingServiceInterface;
use App\Models\Company;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;

class GenerateMonthlyInvoicesCommand extends Command
{
    protected $signature = 'flowflex:generate-monthly-invoices {--period= : YYYY-MM, defaults to previous month}';

    protected $description = 'Generate monthly module invoices for all active companies (idempotent per company+period)';

    public function handle(BillingServiceInterface $billing): int
    {
        $period = $this->option('period')
            ? CarbonImmutable::createFromFormat('Y-m', (string) $this->option('period'))
            : CarbonImmutable::now()->subMonth();

        $companies = Company::query()->withoutGlobalScopes()
            ->where('subscription_status', 'active')
            ->pluck('id');

        foreach ($companies as $companyId) {
            $invoice = $billing->generateMonthlyInvoice($companyId, $period);
            $this->line("{$companyId}: {$invoice->total_formatted} ({$invoice->status})");
        }

        $this->info("Generated/verified {$companies->count()} invoices for {$period->format('Y-m')}.");

        return self::SUCCESS;
    }
}
