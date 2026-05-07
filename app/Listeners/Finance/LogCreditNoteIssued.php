<?php

namespace App\Listeners\Finance;

use App\Events\Finance\CreditNoteIssued;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogCreditNoteIssued implements ShouldQueue
{
    public function handle(CreditNoteIssued $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
