<?php

namespace App\Notifications\Hr;

use App\Models\Hr\LeaveRequest;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRequestedNotification extends FlowFlexNotification
{
    public function __construct(public readonly LeaveRequest $leaveRequest) {}

    public function notificationType(): string
    {
        return 'hr.leave.requested';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $employee = $this->leaveRequest->employee;
        $employeeName = $employee ? "{$employee->first_name} {$employee->last_name}" : 'An employee';
        $leaveType = $this->leaveRequest->leaveType?->name ?? 'leave';
        $startDate = $this->leaveRequest->start_date?->toDateString();
        $endDate = $this->leaveRequest->end_date?->toDateString();

        return (new MailMessage)
            ->subject('New Leave Request')
            ->greeting('Hello!')
            ->line("{$employeeName} has requested {$leaveType} leave from {$startDate} to {$endDate}.")
            ->action('View Request', url('/hr/leave-requests/' . $this->leaveRequest->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        $employee = $this->leaveRequest->employee;

        return [
            'type'             => $this->notificationType(),
            'leave_request_id' => $this->leaveRequest->id,
            'employee_name'    => $employee ? "{$employee->first_name} {$employee->last_name}" : null,
            'leave_type'       => $this->leaveRequest->leaveType?->name,
            'start_date'       => $this->leaveRequest->start_date?->toDateString(),
            'end_date'         => $this->leaveRequest->end_date?->toDateString(),
            'status'           => $this->leaveRequest->status?->value ?? $this->leaveRequest->status,
        ];
    }
}
