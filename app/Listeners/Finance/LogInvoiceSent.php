<?php

namespace App\Listeners\Finance;

use App\Events\Finance\InvoiceSent;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogInvoiceSent implements ShouldQueue
{
    public function handle(InvoiceSent $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
