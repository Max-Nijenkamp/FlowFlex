<?php

declare(strict_types=1);

namespace App\Services\Core;

use App\Models\Core\NotificationPreference;
use App\Models\User;

class NotificationPreferenceService
{
    /**
     * Resolve the enabled channels for a user + notification type.
     * No stored preference = both channels on (default-enabled).
     *
     * @return list<string>
     */
    public function channelsFor(User $user, string $type): array
    {
        $preference = NotificationPreference::query()
            ->where('user_id', $user->id)
            ->where('notification_type', $type)
            ->first();

        $channels = [];

        if ($preference->in_app_enabled ?? true) {
            $channels[] = 'database';
        }

        if (($preference->email_enabled ?? true) && $user->email_deliverable) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
