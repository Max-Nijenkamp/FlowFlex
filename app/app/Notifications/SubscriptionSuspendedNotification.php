<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Support\Notifications\FlowFlexNotification;

class SubscriptionSuspendedNotification extends FlowFlexNotification
{
    public function __construct(private readonly string $reason) {}

    public function notificationType(): string
    {
        return 'subscription-suspended';
    }

    public function title(): string
    {
        return 'Your workspace is suspended';
    }

    public function body(): string
    {
        return "Billing suspended your workspace: {$this->reason}. Settle the open invoice to reactivate.";
    }

    /** Suspension mail must always deliver, whatever the preference rows say. */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }
}
