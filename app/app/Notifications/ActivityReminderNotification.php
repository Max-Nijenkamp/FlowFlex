<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Support\Notifications\FlowFlexNotification;

/** Due/overdue CRM task surfaced by TaskReminderCommand (crm.activities). */
class ActivityReminderNotification extends FlowFlexNotification
{
    public function __construct(
        private readonly string $subject,
        private readonly bool $isOverdue,
    ) {}

    public function notificationType(): string
    {
        return 'activity-reminder';
    }

    public function title(): string
    {
        return $this->isOverdue ? 'Task overdue' : 'Task due soon';
    }

    public function body(): string
    {
        return $this->isOverdue
            ? "\"{$this->subject}\" is past its due date."
            : "\"{$this->subject}\" is due within the next day.";
    }
}
