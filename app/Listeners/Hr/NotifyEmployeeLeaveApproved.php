<?php

namespace App\Listeners\Hr;

use App\Events\Hr\LeaveApproved;
use App\Models\Tenant;
use App\Notifications\Hr\LeaveApprovedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEmployeeLeaveApproved implements ShouldQueue
{
    public function handle(LeaveApproved $event): void
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
            $tenant->notify(new LeaveApprovedNotification($leaveRequest));
        }
    }
}
