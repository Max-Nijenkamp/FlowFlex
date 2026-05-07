<?php

namespace App\Listeners\Hr;

use App\Events\Hr\PayRunCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPayRunCreated implements ShouldQueue
{
    public function handle(PayRunCreated $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
