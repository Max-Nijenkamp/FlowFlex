<?php

declare(strict_types=1);

namespace App\Listeners\Crm;

use App\Events\Crm\DealWon;
use App\Models\Crm\Account;
use App\Models\Crm\Contact;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;

/** Won deal → the primary contact becomes a customer (crm.contacts edge). */
class AdvanceLifecycleOnDealWonListener implements ShouldQueue
{
    public string $queue = 'domain-events';

    public function handle(DealWon $event): void
    {
        WithCompanyContext::restore($event->company_id);

        if ($event->contact_id !== null) {
            Contact::query()->whereKey($event->contact_id)->update(['lifecycle_stage' => 'customer']);
        }

        if ($event->account_id !== null) {
            // Touch keeps account freshness visible on the view page.
            Account::query()->whereKey($event->account_id)->first()?->touch();
        }
    }
}
