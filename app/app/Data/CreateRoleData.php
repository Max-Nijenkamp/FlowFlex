<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CreateRoleData extends Data
{
    /** @param list<string> $permissions */
    public function __construct(
        public string $name,
        public array $permissions = [],
    ) {}
}
