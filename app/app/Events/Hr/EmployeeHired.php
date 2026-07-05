<?php

declare(strict_types=1);

namespace App\Events\Hr;

use Illuminate\Foundation\Events\Dispatchable;

/** Fired on employee creation (hr.profiles, event-bus contract). */
class EmployeeHired
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $employee_id,
        public readonly ?string $user_id,
        public readonly string $start_date,
        public readonly string $job_title,
    ) {}
}
