<?php

namespace App\Listeners\Crm;

use App\Events\Crm\DealWon;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDealWon implements ShouldQueue
{
    public function handle(DealWon $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
