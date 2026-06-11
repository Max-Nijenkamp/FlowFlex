<?php

declare(strict_types=1);

namespace App\Support\Settings;

/**
 * Default values per settings group. Read by CompanyScopedSettingsRepository
 * for properties a company has not persisted yet. Keep in sync with the
 * app/Settings/* classes.
 */
class SettingsDefaults
{
    /** @return array<string, mixed> */
    public static function forGroup(string $group): array
    {
        return match ($group) {
            'company_identity' => [
                'name' => '',
                'slug' => '',
                'logo_path' => null,
                'favicon_path' => null,
                'primary_color' => '#38BDF8',
            ],
            'company_locale' => [
                'timezone' => 'Europe/Amsterdam',
                'locale' => 'en',
                'date_format' => 'd-m-Y',
                'currency' => 'EUR',
                'currency_position' => 'before',
                'decimal_places' => 2,
            ],
            'company_business' => [
                'fiscal_year_start_month' => 1,
                'week_start' => 'monday',
                'holiday_calendar_country' => 'NL',
            ],
            'company_privacy' => [
                'data_retention_months' => 36,
                'dsar_email' => null,
                'consent_logging_enabled' => true,
            ],
            default => [],
        };
    }
}
