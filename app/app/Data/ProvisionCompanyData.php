<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class ProvisionCompanyData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $owner_email,
        public readonly string $timezone = 'Europe/Amsterdam',
        public readonly string $locale = 'nl',
        public readonly string $currency = 'EUR',
    ) {}

    /** @return array<string, array<int, string>> */
    public static function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'owner_email' => ['required', 'email'],
            'timezone' => ['required', 'timezone'],
            'locale' => ['required', 'string', 'max:10'],
            'currency' => ['required', 'string', 'size:3'],
        ];
    }
}
