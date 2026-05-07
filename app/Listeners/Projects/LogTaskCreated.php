<?php

namespace App\Listeners\Projects;

use App\Events\Projects\TaskCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogTaskCreated implements ShouldQueue
{
    public function handle(TaskCreated $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
