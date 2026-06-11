<?php

declare(strict_types=1);

namespace App\Listeners\Finance;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Events\HR\PayrollRunApproved;
use App\Models\Company;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PostPayrollJournalEntryListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(PayrollRunApproved $event): void
    {
        $company = Company::query()->withoutGlobalScopes()->findOrFail($event->company_id);
        app(CompanyContext::class)->set($company);
        setPermissionsTeamId($company->id);

        $withholdings = $event->total_gross_cents - $event->total_net_cents;

        // Balanced entry: gross wages expense / withholdings liability / net payable.
        $lines = [
            ['account_code' => '6000', 'debit_cents' => $event->total_gross_cents],
            ['account_code' => '2100', 'credit_cents' => $event->total_net_cents],
        ];
        if ($withholdings > 0) {
            $lines[] = ['account_code' => '2200', 'credit_cents' => $withholdings];
        }

        // ClosedPeriodException propagates -> queue retries per contract.
        app(LedgerServiceInterface::class)->post(
            reference: "PAYRUN-{$event->period_start->format('Y-m')}",
            description: "Payroll run {$event->period_start->toDateString()} – {$event->period_end->toDateString()}",
            entryDate: now()->toDateString(),
            lines: $lines,
            sourceType: 'payroll_run',
            sourceId: $event->payroll_run_id,
        );
    }
}
