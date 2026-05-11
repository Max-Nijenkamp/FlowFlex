<?php

declare(strict_types=1);

namespace App\Events\HR;

use App\Models\Company;
use App\Models\HR\LeaveRequest;

class LeaveApproved
{
    public function __construct(
        public readonly Company $company,
        public readonly LeaveRequest $request,
    ) {}
}
