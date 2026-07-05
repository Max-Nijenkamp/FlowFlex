<?php

declare(strict_types=1);

namespace App\Events\Hr;

use Illuminate\Foundation\Events\Dispatchable;

/** Fired on termination (hr.profiles/offboarding, event-bus contract). */
class EmployeeOffboarded
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $employee_id,
        public readonly ?string $user_id,
        public readonly string $termination_date,
        public readonly string $reason,
    ) {}
}
