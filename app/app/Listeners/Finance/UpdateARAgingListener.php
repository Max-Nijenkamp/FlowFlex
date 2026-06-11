<?php

declare(strict_types=1);

namespace App\Listeners\Finance;

use App\Events\Finance\InvoicePaid;
use App\Models\Finance\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/** InvoicePaid → reset the dunning escalation level (event-bus contract). */
class UpdateARAgingListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(InvoicePaid $event): void
    {
        Invoice::query()->withoutGlobalScopes()
            ->where('company_id', $event->company_id)
            ->whereKey($event->invoice_id)
            ->update(['last_dunning_level' => 0]);
    }
}
