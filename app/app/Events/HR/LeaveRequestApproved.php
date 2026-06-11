<?php

declare(strict_types=1);

namespace App\Events\HR;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class LeaveRequestApproved
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $leave_request_id,
        public readonly string $employee_id,
        public readonly string $leave_type_id,
        public readonly CarbonImmutable $start_date,
        public readonly CarbonImmutable $end_date,
        public readonly float $days,
    ) {}
}
