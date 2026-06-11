<?php

declare(strict_types=1);

namespace App\Listeners\HR;

use App\Events\HR\LeaveRequestApproved;
use App\Models\HR\Shift;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class BlockShiftsOnLeaveListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(LeaveRequestApproved $event): void
    {
        // Unassign the employee from shifts in the leave range — gaps flagged
        // by the null employee_id (event-bus contract).
        Shift::query()->withoutGlobalScopes()
            ->where('company_id', $event->company_id)
            ->where('employee_id', $event->employee_id)
            ->whereDate('date', '>=', $event->start_date->toDateString())
            ->whereDate('date', '<=', $event->end_date->toDateString())
            ->where('status', '!=', 'cancelled')
            ->update(['employee_id' => null]);
    }
}
