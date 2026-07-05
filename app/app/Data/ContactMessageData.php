<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;

class ContactMessageData extends Data
{
    public function __construct(
        #[Max(120)]
        public string $name,
        #[Email, Max(255)]
        public string $email,
        #[Max(50)]
        public ?string $company_size,
        #[Max(5000)]
        public string $message,
    ) {}
}
