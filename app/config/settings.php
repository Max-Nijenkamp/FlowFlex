<?php

declare(strict_types=1);

use App\Support\Settings\CompanyScopedSettingsRepository;

return [

    'settings' => [
        App\Settings\CompanyIdentitySettings::class,
        App\Settings\CompanyLocaleSettings::class,
        App\Settings\CompanyBusinessSettings::class,
        App\Settings\CompanyPrivacySettings::class,
    ],

    'setting_class_path' => app_path('Settings'),

    // Unused: defaults come from config/company-settings.php via the
    // company-scoped repository, so no spatie settings-migrations exist.
    'migrations_paths' => [
        database_path('settings'),
    ],

    'default_repository' => 'database',

    'repositories' => [
        'database' => [
            'type' => CompanyScopedSettingsRepository::class,
            'model' => null,
            'table' => null,
            'connection' => null,
        ],
    ],

    'encoder' => null,
    'decoder' => null,

    /*
     * Spatie's settings cache keys ignore the tenant, so caching MUST stay
     * disabled — enabling it would leak one company's settings to another.
     * (Deviation from the spec's "10 min cache", recorded in the module's
     * architecture note 2026-07-04.)
     */
    'cache' => [
        'default' => null,
        'stores' => [],
    ],

    'global_casts' => [
        DateTimeInterface::class => Spatie\LaravelSettings\SettingsCasts\DateTimeInterfaceCast::class,
        DateTimeZone::class => Spatie\LaravelSettings\SettingsCasts\DateTimeZoneCast::class,
    ],

    'auto_discover_settings' => [],

    'discovered_settings_cache_path' => base_path('bootstrap/cache'),
];
