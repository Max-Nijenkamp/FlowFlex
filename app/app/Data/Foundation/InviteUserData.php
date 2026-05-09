<?php

declare(strict_types=1);

namespace App\Data\Foundation;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class InviteUserData extends Data
{
    public function __construct(
        #[Required, Max(100)]
        public readonly string $first_name,

        #[Required, Max(100)]
        public readonly string $last_name,

        #[Required, Email]
        public readonly string $email,

        #[Required]
        public readonly string $role,

        public readonly ?string $locale = null,
        public readonly ?string $timezone = null,
    ) {}
}
