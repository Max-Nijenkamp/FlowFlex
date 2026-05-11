<?php

declare(strict_types=1);

namespace App\Events\HR;

use App\Models\Company;
use App\Models\HR\PayrollRun;

class PayrollRunPaid
{
    public function __construct(
        public readonly Company $company,
        public readonly PayrollRun $run,
    ) {}
}
