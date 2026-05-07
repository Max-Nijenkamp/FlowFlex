<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TaskOverdue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTaskOverdue implements ShouldQueue
{
    public function handle(TaskOverdue $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
