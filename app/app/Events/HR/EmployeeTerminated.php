<?php

declare(strict_types=1);

namespace App\Events\HR;

use App\Models\Company;
use App\Models\HR\Employee;

class EmployeeTerminated
{
    public function __construct(
        public readonly Company $company,
        public readonly Employee $employee,
    ) {}
}
