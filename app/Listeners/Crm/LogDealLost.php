<?php

namespace App\Listeners\Crm;

use App\Events\Crm\DealLost;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogDealLost implements ShouldQueue
{
    public function handle(DealLost $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
