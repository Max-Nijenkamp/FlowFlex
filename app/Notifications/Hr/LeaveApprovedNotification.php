<?php

namespace App\Notifications\Hr;

use App\Models\Hr\LeaveRequest;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveApprovedNotification extends FlowFlexNotification
{
    public function __construct(public readonly LeaveRequest $leaveRequest) {}

    public function notificationType(): string
    {
        return 'hr.leave.approved';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $leaveType = $this->leaveRequest->leaveType?->name ?? 'leave';
        $startDate = $this->leaveRequest->start_date?->toDateString();
        $endDate = $this->leaveRequest->end_date?->toDateString();

        return (new MailMessage)
            ->subject('Leave Request Approved')
            ->greeting('Good news!')
            ->line("Your {$leaveType} leave request has been approved.")
            ->line("Approved dates: {$startDate} to {$endDate}.")
            ->action('View Request', url('/hr/leave-requests/' . $this->leaveRequest->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => $this->notificationType(),
            'leave_request_id' => $this->leaveRequest->id,
            'leave_type'       => $this->leaveRequest->leaveType?->name,
            'start_date'       => $this->leaveRequest->start_date?->toDateString(),
            'end_date'         => $this->leaveRequest->end_date?->toDateString(),
        ];
    }
}
