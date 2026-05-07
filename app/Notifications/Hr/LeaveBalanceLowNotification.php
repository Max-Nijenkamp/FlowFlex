<?php

namespace App\Notifications\Hr;

use App\Models\Hr\LeaveBalance;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class LeaveBalanceLowNotification extends FlowFlexNotification
{
    public function __construct(public readonly LeaveBalance $balance) {}

    public function notificationType(): string
    {
        return 'hr.leave.balance_low';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $leaveType = $this->balance->leaveType?->name ?? 'leave';
        $remaining = $this->balance->remainingDays();

        return (new MailMessage)
            ->subject('Leave Balance Running Low')
            ->greeting('Heads up!')
            ->line("Your {$leaveType} balance is running low.")
            ->line("Remaining days: {$remaining}.")
            ->action('View Leave Balance', url('/hr/leave-requests'))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'           => $this->notificationType(),
            'leave_balance_id' => $this->balance->id,
            'leave_type'     => $this->balance->leaveType?->name,
            'remaining_days' => $this->balance->remainingDays(),
        ];
    }
}
