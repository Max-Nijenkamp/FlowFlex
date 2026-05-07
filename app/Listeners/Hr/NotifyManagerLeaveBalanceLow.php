<?php

namespace App\Listeners\Hr;

use App\Events\Hr\LeaveBalanceLow;
use App\Models\Tenant;
use App\Notifications\Hr\LeaveBalanceLowNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyManagerLeaveBalanceLow implements ShouldQueue
{
    public function handle(LeaveBalanceLow $event): void
    {
        $balance = $event->balance;
        $employee = $balance->employee;

        if (! $employee || ! $employee->email) {
            return;
        }

        // Notify the employee tenant about their low leave balance
        $tenant = Tenant::where('company_id', $balance->company_id)
            ->where('email', $employee->email)
            ->first();

        if ($tenant) {
            $tenant->notify(new LeaveBalanceLowNotification($balance));
        }
    }
}
