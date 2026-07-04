<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class ProvisionCompanyData extends Data
{
    public function __construct(
        public string $name,
        public string $owner_email,
        public string $timezone = 'Europe/Amsterdam',
        public string $locale = 'en',
        public string $currency = 'EUR',
    ) {}
}
