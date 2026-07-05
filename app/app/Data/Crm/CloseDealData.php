<?php

declare(strict_types=1);

namespace App\Data\Crm;

use Spatie\LaravelData\Data;

class CloseDealData extends Data
{
    public function __construct(
        public string $dealId,
        public string $outcome, // won | lost
        public ?string $lostReason = null,
        public ?string $lostTo = null,
    ) {}
}
