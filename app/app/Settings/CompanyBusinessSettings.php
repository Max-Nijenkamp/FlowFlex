<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanyBusinessSettings extends Settings
{
    public int $fiscal_year_start_month;

    public string $week_start;

    public string $holiday_calendar_country;

    public int $max_upload_mb;

    public static function group(): string
    {
        return 'company_business';
    }
}
