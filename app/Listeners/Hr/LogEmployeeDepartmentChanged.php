<?php

namespace App\Listeners\Hr;

use App\Events\Hr\EmployeeDepartmentChanged;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogEmployeeDepartmentChanged implements ShouldQueue
{
    public function handle(EmployeeDepartmentChanged $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
