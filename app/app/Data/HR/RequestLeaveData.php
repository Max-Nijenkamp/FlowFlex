<?php

declare(strict_types=1);

namespace App\Data\HR;

use Spatie\LaravelData\Data;

class RequestLeaveData extends Data
{
    public function __construct(
        public readonly string $employee_id,
        public readonly string $policy_id,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly float $days_requested,
        public readonly ?string $reason = null,
    ) {}
}
