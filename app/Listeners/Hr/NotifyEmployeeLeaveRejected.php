<?php

namespace App\Listeners\Hr;

use App\Events\Hr\LeaveRejected;
use App\Models\Tenant;
use App\Notifications\Hr\LeaveRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEmployeeLeaveRejected implements ShouldQueue
{
    public function handle(LeaveRejected $event): void
    {
        $leaveRequest = $event->leaveRequest;
        $employee = $leaveRequest->employee;

        if (! $employee || ! $employee->email) {
            return;
        }

        $tenant = Tenant::where('company_id', $leaveRequest->company_id)
            ->where('email', $employee->email)
            ->first();

        if ($tenant) {
            $tenant->notify(new LeaveRejectedNotification($leaveRequest));
        }
    }
}
