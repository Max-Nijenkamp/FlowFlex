<?php

declare(strict_types=1);

/*
 * Per-group defaults for the company-scoped settings repository: a company
 * with no saved rows reads these (core.company-settings). Keys must match
 * the settings class properties exactly.
 */
return [

    'defaults' => [

        'company_identity' => [
            'name' => '',
            'slug' => '',
            'logo_path' => null,
            'favicon_path' => null,
            'primary_color' => '#4F46E5',
        ],

        'company_locale' => [
            'timezone' => 'UTC',
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
            'data_retention_months' => 24,
            'dsar_email' => null,
            'consent_logging_enabled' => false,
        ],
    ],
];
