<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TimeEntryApproved;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTimeEntryApproved implements ShouldQueue
{
    public function handle(TimeEntryApproved $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
