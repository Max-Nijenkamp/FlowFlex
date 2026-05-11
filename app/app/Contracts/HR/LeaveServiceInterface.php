<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Data\HR\RequestLeaveData;
use App\Models\Company;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\User;

interface LeaveServiceInterface
{
    public function requestLeave(RequestLeaveData $data, Company $company): LeaveRequest;

    public function approve(LeaveRequest $request, User $approver): LeaveRequest;

    public function reject(LeaveRequest $request, ?string $reason = null): LeaveRequest;

    public function cancel(LeaveRequest $request): LeaveRequest;

    public function calculateBalance(string $employeeId, string $policyId, int $year): LeaveBalance;
}
