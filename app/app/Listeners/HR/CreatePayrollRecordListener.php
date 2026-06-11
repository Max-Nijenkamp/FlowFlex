<?php

declare(strict_types=1);

namespace App\Listeners\HR;

use App\Events\HR\EmployeeHired;
use App\Models\HR\PayrollEmployee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePayrollRecordListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(EmployeeHired $event): void
    {
        // Stub record, status `incomplete` until HR enters compensation (event-bus contract).
        PayrollEmployee::query()->withoutGlobalScopes()->firstOrCreate(
            ['employee_id' => $event->employee_id],
            ['company_id' => $event->company_id],
        );
    }
}
