<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class CompanyIdentitySettings extends Settings
{
    public string $name;

    public string $slug;

    public ?string $logo_path;

    public ?string $favicon_path;

    public string $primary_color;

    public static function group(): string
    {
        return 'company_identity';
    }
}
