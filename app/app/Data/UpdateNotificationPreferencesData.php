<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class UpdateNotificationPreferencesData extends Data
{
    /** @param list<array{notification_type: string, in_app_enabled: bool, email_enabled: bool}> $preferences */
    public function __construct(
        public readonly array $preferences,
    ) {}

    /** @return array<string, mixed> */
    public static function rules(): array
    {
        return [
            'preferences' => ['required', 'array'],
            'preferences.*.notification_type' => ['required', 'string'],
            'preferences.*.in_app_enabled' => ['required', 'boolean'],
            'preferences.*.email_enabled' => ['required', 'boolean'],
        ];
    }
}
