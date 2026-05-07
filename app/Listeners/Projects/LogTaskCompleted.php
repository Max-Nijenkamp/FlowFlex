<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTaskCompleted implements ShouldQueue
{
    public function handle(TaskCompleted $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
