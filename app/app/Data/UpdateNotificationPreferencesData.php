<?php

declare(strict_types=1);

namespace App\Data;

use App\Services\NotificationPreferenceService;
use InvalidArgumentException;
use Spatie\LaravelData\Data;

class UpdateNotificationPreferencesData extends Data
{
    /** @param array<string, array{in_app: bool, email: bool}> $preferences keyed by notification type */
    public function __construct(
        public array $preferences,
    ) {
        foreach (array_keys($preferences) as $type) {
            if (! NotificationPreferenceService::isKnownType($type)) {
                throw new InvalidArgumentException("Unknown notification type [{$type}].");
            }
        }
    }
}
