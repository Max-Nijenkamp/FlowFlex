<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class AssignRolesData extends Data
{
    /** @param list<string> $roles role names within the current company team */
    public function __construct(
        public string $userId,
        public array $roles,
    ) {}
}
