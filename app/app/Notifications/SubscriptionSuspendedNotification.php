<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use App\Support\Notifications\FlowFlexNotification;

class SubscriptionSuspendedNotification extends FlowFlexNotification
{
    public function __construct(
        public readonly string $reason,
    ) {
        parent::__construct();
    }

    public function title(): string
    {
        return 'Your FlowFlex workspace is suspended';
    }

    public function body(): string
    {
        return 'Payment could not be collected. Update your payment method to restore access.';
    }

    /**
     * Suspension mail must reach the owner even though the panel is blocked —
     * force the mail channel regardless of preferences.
     *
     * @return list<string>
     */
    public function via(User $notifiable): array
    {
        return array_values(array_unique([...parent::via($notifiable), 'mail']));
    }
}
