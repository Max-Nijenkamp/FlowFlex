<?php

namespace App\Listeners\Finance;

use App\Events\Finance\InvoicePaid;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogInvoicePaid implements ShouldQueue
{
    public function handle(InvoicePaid $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
