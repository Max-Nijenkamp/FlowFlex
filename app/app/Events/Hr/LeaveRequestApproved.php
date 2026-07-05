<?php

declare(strict_types=1);

namespace App\Events\Hr;

use Illuminate\Foundation\Events\Dispatchable;

/** Fired on approval (hr.leave, event-bus contract — payroll consumes later). */
class LeaveRequestApproved
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $leave_request_id,
        public readonly string $employee_id,
        public readonly string $start_date,
        public readonly string $end_date,
        public readonly float $days,
    ) {}
}
