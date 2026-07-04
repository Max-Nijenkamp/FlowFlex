<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class AcceptInvitationData extends Data
{
    public function __construct(
        public string $token,
        public string $first_name,
        public string $last_name,
        public string $password,
    ) {}
}
