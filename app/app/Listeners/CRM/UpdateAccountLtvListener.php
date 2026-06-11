<?php

declare(strict_types=1);

namespace App\Listeners\CRM;

use App\Events\Finance\InvoicePaid;
use App\Models\CRM\Account;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateAccountLtvListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(InvoicePaid $event): void
    {
        // No-op when the invoice has no CRM account link (event-bus contract).
        if ($event->crm_account_id === null) {
            return;
        }

        Account::query()->withoutGlobalScopes()
            ->where('company_id', $event->company_id)
            ->whereKey($event->crm_account_id)
            ->increment('lifetime_value_cents', $event->amount_cents);
    }
}
