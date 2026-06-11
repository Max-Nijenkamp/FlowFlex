<?php

declare(strict_types=1);

namespace App\Listeners\HR;

use App\Events\HR\EmployeeOffboarded;
use App\Models\HR\PayrollEmployee;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class FinalPayListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(EmployeeOffboarded $event): void
    {
        // Flag the final payroll run incl. leave payout (event-bus contract).
        PayrollEmployee::query()->withoutGlobalScopes()
            ->where('employee_id', $event->employee_id)
            ->update(['final_pay_flagged' => true]);
    }
}
