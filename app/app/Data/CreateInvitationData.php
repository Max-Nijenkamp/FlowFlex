<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateInvitationData extends Data
{
    public function __construct(
        public string $email,
        public string $role,
    ) {}
}
