<?php

namespace App\Listeners\Hr;

use App\Events\Hr\LeaveRequested;
use App\Models\Tenant;
use App\Notifications\Hr\LeaveRequestedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyManagerOfLeaveRequest implements ShouldQueue
{
    public function handle(LeaveRequested $event): void
    {
        $leaveRequest = $event->leaveRequest;
        $employee = $leaveRequest->employee;

        // Notify the employee's manager if they have one linked to a Tenant.
        if ($employee && $employee->manager_id) {
            $managerEmployee = $employee->manager;

            // Find tenant by email match.
            if ($managerEmployee && $managerEmployee->email) {
                $manager = Tenant::where('company_id', $leaveRequest->company_id)
                    ->where('email', $managerEmployee->email)
                    ->first();

                if ($manager) {
                    $manager->notify(new LeaveRequestedNotification($leaveRequest));
                }
            }
        }
    }
}
