<?php

namespace App\Listeners\Hr;

use App\Events\Hr\EmployeeProfileCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogEmployeeProfileCreated implements ShouldQueue
{
    public function handle(EmployeeProfileCreated $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
