<?php

declare(strict_types=1);

namespace App\Events\HR;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class TimesheetApproved
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $timesheet_id,
        public readonly string $employee_id,
        public readonly CarbonImmutable $period_start,
        public readonly CarbonImmutable $period_end,
        public readonly int $total_minutes,
    ) {}
}
