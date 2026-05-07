<?php

namespace App\Listeners\Hr;

use App\Events\Hr\EmployeeProfileUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogEmployeeProfileUpdated implements ShouldQueue
{
    public function handle(EmployeeProfileUpdated $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
