<?php

declare(strict_types=1);

namespace App\Events\HR;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class EmployeeHired
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $employee_id,
        public readonly ?string $user_id,
        public readonly CarbonImmutable $start_date,
        public readonly string $job_title,
    ) {}
}
