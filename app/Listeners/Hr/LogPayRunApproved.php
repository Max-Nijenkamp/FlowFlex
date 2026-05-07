<?php

namespace App\Listeners\Hr;

use App\Events\Hr\PayRunApproved;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogPayRunApproved implements ShouldQueue
{
    public function handle(PayRunApproved $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
