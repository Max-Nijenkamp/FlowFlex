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
        // Eager-load employee and their manager in one query to avoid N+1.
        $leaveRequest = $event->leaveRequest->load('employee.manager');
        $employee     = $leaveRequest->employee;

        if (! $employee || ! $employee->manager_id) {
            return;
        }

        $managerEmployee = $employee->manager;

        if (! $managerEmployee || ! $managerEmployee->email) {
            // Manager employee record has no email — cannot resolve a Tenant. Skip silently.
            logger()->debug('NotifyManagerOfLeaveRequest: manager employee has no email', [
                'leave_request_id' => $leaveRequest->id,
                'manager_id'       => $employee->manager_id,
            ]);

            return;
        }

        // Resolve the Tenant account for the manager by matching email within the same company.
        $managerTenant = Tenant::where('company_id', $leaveRequest->company_id)
            ->where('email', $managerEmployee->email)
            ->first();

        if (! $managerTenant) {
            logger()->debug('NotifyManagerOfLeaveRequest: no matching Tenant found for manager email', [
                'leave_request_id' => $leaveRequest->id,
                'manager_email'    => $managerEmployee->email,
            ]);

            return;
        }

        $managerTenant->notify(new LeaveRequestedNotification($leaveRequest));
    }
}
