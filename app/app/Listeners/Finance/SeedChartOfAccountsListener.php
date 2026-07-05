<?php

declare(strict_types=1);

namespace App\Listeners\Finance;

use App\Events\ModuleActivated;
use App\Services\Finance\LedgerService;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;

/** Activating finance.ledger seeds the default SME chart of accounts. */
class SeedChartOfAccountsListener implements ShouldQueue
{
    public string $queue = 'domain-events';

    public function handle(ModuleActivated $event): void
    {
        if ($event->module_key !== 'finance.ledger') {
            return;
        }

        WithCompanyContext::restore($event->company_id);

        LedgerService::ensureDefaultChartOfAccounts($event->company_id);
    }
}
