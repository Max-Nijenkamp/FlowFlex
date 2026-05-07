<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TimeEntryRejected;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTimeEntryRejected implements ShouldQueue
{
    public function handle(TimeEntryRejected $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
