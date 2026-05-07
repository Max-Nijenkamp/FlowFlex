<?php

namespace App\Notifications\Hr;

use App\Models\Hr\LeaveRequest;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveRejectedNotification extends FlowFlexNotification
{
    public function __construct(public readonly LeaveRequest $leaveRequest) {}

    public function notificationType(): string
    {
        return 'hr.leave.rejected';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $leaveType = $this->leaveRequest->leaveType?->name ?? 'leave';

        $mail = (new MailMessage)
            ->subject('Leave Request Rejected')
            ->greeting('Hello!')
            ->line("Your {$leaveType} leave request has been rejected.");

        if ($this->leaveRequest->rejection_reason) {
            $mail->line("Reason: {$this->leaveRequest->rejection_reason}");
        }

        return $mail
            ->action('View Request', url('/hr/leave-requests/' . $this->leaveRequest->id . '/edit'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'             => $this->notificationType(),
            'leave_request_id' => $this->leaveRequest->id,
            'leave_type'       => $this->leaveRequest->leaveType?->name,
            'rejection_reason' => $this->leaveRequest->rejection_reason,
        ];
    }
}
