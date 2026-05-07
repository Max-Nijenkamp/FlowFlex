<?php

namespace App\Listeners\Hr;

use App\Events\Hr\EmployeeRoleChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogEmployeeRoleChanged implements ShouldQueue
{
    public function handle(EmployeeRoleChanged $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
