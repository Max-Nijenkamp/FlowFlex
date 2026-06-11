<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanyPrivacySettings extends Settings
{
    public int $data_retention_months;

    public ?string $dsar_email;

    public bool $consent_logging_enabled;

    public static function group(): string
    {
        return 'company_privacy';
    }
}
