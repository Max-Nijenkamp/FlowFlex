<?php

namespace App\Listeners\Finance;

use App\Events\Finance\InvoiceCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogInvoiceCreated implements ShouldQueue
{
    public function handle(InvoiceCreated $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
