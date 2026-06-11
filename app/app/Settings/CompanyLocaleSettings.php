<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanyLocaleSettings extends Settings
{
    public string $timezone;

    public string $locale;

    public string $date_format;

    public string $currency;

    public string $currency_position;

    public int $decimal_places;

    public static function group(): string
    {
        return 'company_locale';
    }
}
