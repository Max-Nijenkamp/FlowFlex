<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Data\HR\SubmitLeaveRequestData;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

interface LeaveServiceInterface
{
    /** Throws InsufficientLeaveBalanceException. Auto-approves no-approval types. */
    public function submit(SubmitLeaveRequestData $data): LeaveRequest;

    /** Throws CannotApproveOwnRequestException + invalid-transition. Fires LeaveRequestApproved. */
    public function approve(string $leaveRequestId): LeaveRequest;

    public function reject(string $leaveRequestId, string $reason): LeaveRequest;

    public function cancel(string $leaveRequestId): LeaveRequest;

    /** @return Collection<int, LeaveBalance> */
    public function balanceFor(string $employeeId, int $year): Collection;

    /** Weekends excluded; public-holiday calendar arrives with i18n holidays (assumed weekdays-only v1). */
    public function calculateWorkingDays(CarbonImmutable $start, CarbonImmutable $end): float;

    /** Monthly accrual — idempotent upsert per (employee, type, year). */
    public function accrueMonthly(): void;
}
