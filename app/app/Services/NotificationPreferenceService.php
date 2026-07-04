<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\User;

/**
 * Channel resolution for every FlowFlexNotification (core.notifications/
 * preferences): the single read surface deciding database vs mail delivery.
 */
class NotificationPreferenceService
{
    /**
     * The notification-type registry. Domains append their types here as
     * their modules ship.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'module-activated' => 'A module is switched on or off',
        'subscription-suspended' => 'Billing suspends the workspace',
        'invitation-accepted' => 'An invited teammate joins',
        'ownership-transferred' => 'Workspace ownership changes',
    ];

    public static function isKnownType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }

    /** @return list<string> notification channels ('database', 'mail') */
    public function channelsFor(User $user, string $type): array
    {
        $preference = NotificationPreference::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('notification_type', $type)
            ->first();

        $channels = [];

        if ($preference === null || $preference->in_app_enabled) {
            $channels[] = 'database';
        }

        if ($preference === null || $preference->email_enabled) {
            $channels[] = 'mail';
        }

        return $channels;
    }
}
