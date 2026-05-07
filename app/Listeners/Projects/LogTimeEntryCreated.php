<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TimeEntryCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTimeEntryCreated implements ShouldQueue
{
    public function handle(TimeEntryCreated $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
