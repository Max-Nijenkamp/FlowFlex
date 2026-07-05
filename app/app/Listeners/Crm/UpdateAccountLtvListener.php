<?php

declare(strict_types=1);

namespace App\Listeners\Crm;

use App\Events\Finance\InvoicePaid;
use App\Models\Crm\Account;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * InvoicePaid → bump the CRM account lifetime value (crm.contacts edge).
 * No-op when the customer has no CRM link.
 */
class UpdateAccountLtvListener implements ShouldQueue
{
    public string $queue = 'domain-events';

    public function handle(InvoicePaid $event): void
    {
        if ($event->crm_account_id === null) {
            return;
        }

        WithCompanyContext::restore($event->company_id);

        $account = Account::query()->find($event->crm_account_id);

        if ($account instanceof Account) {
            $account->lifetime_value_cents += $event->total_cents;
            $account->save();
        }
    }
}
