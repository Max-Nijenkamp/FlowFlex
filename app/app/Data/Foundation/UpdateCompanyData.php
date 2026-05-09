<?php

declare(strict_types=1);

namespace App\Data\Foundation;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class UpdateCompanyData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public readonly string $name,

        #[Required, Max(100)]
        public readonly string $slug,

        #[Required, Email]
        public readonly string $email,

        #[Required, Max(100)]
        public readonly string $timezone,

        #[Required, Max(10)]
        public readonly string $locale,

        #[Required, Max(3)]
        public readonly string $currency,

        public readonly ?array $branding = null,
        public readonly ?array $ai_config = null,
    ) {}
}
