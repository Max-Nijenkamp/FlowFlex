<?php

declare(strict_types=1);

namespace App\Events\HR;

use Carbon\CarbonImmutable;
use Illuminate\Foundation\Events\Dispatchable;

/** Payload per architecture/event-bus — character-exact. */
class PayrollRunApproved
{
    use Dispatchable;

    public function __construct(
        public readonly string $company_id,
        public readonly string $payroll_run_id,
        public readonly CarbonImmutable $period_start,
        public readonly CarbonImmutable $period_end,
        public readonly int $total_gross_cents,
        public readonly int $total_net_cents,
        public readonly string $currency,
    ) {}
}
