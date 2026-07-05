<?php

declare(strict_types=1);

namespace App\Services\Hr;

use App\Data\Hr\SubmitLeaveRequestData;
use App\Events\Hr\LeaveRequestApproved;
use App\Exceptions\Hr\LeaveOverlapException;
use App\Models\Hr\Employee;
use App\Models\Hr\LeaveBalance;
use App\Models\Hr\LeaveRequest;
use App\Models\Hr\LeaveType;
use App\Models\User;
use App\States\Hr\LeaveRequest\Approved;
use App\States\Hr\LeaveRequest\Cancelled;
use App\States\Hr\LeaveRequest\Rejected;
use App\States\Hr\LeaveRequest\Submitted;
use App\Support\Services\AuditLogger;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Leave workflow (hr.leave): working-day counting, overlap detection
 * against approved+pending leave, balance bookkeeping (pending on
 * submit, taken on approval), auto-approve for types that allow it,
 * and the annual accrual/carry-over run.
 */
class LeaveService
{
    public function submit(SubmitLeaveRequestData $data): LeaveRequest
    {
        $start = Carbon::parse($data->startDate);
        $end = Carbon::parse($data->endDate);

        if ($end->lessThan($start)) {
            throw ValidationException::withMessages(['end_date' => 'End date must be on or after the start date.']);
        }

        $days = self::workingDays($start, $end);

        if ($days <= 0) {
            throw ValidationException::withMessages(['start_date' => 'The range contains no working days.']);
        }

        if ($this->overlaps($data->employeeId, $start, $end)) {
            throw LeaveOverlapException::make();
        }

        /** @var LeaveType $type */
        $type = LeaveType::query()->findOrFail($data->leaveTypeId);

        return DB::transaction(function () use ($data, $start, $end, $days, $type): LeaveRequest {
            /** @var LeaveRequest $request */
            $request = LeaveRequest::query()->create([
                'company_id' => app(CompanyContext::class)->current()->id,
                'employee_id' => $data->employeeId,
                'leave_type_id' => $type->id,
                'start_date' => $start->toDateString(),
                'end_date' => $end->toDateString(),
                'days_requested' => $days,
                'note' => $data->note,
            ]);

            $request->status->transitionTo(Submitted::class);

            $balance = $this->balanceFor($request);
            $balance->pending_days = number_format((float) $balance->pending_days + $days, 2, '.', '');
            $balance->save();

            if (! $type->requires_approval) {
                return $this->approve($request->refresh());
            }

            return $request->refresh();
        });
    }

    public function approve(LeaveRequest $request): LeaveRequest
    {
        return DB::transaction(function () use ($request): LeaveRequest {
            $request->status->transitionTo(Approved::class);
            $request->update(['approved_by' => Auth::id(), 'approved_at' => now()]);

            $balance = $this->balanceFor($request);
            $balance->pending_days = number_format(max(0, (float) $balance->pending_days - (float) $request->days_requested), 2, '.', '');
            $balance->taken_days = number_format((float) $balance->taken_days + (float) $request->days_requested, 2, '.', '');
            $balance->save();

            LeaveRequestApproved::dispatch(
                $request->company_id,
                $request->id,
                $request->employee_id,
                $request->start_date->toDateString(),
                $request->end_date->toDateString(),
                (float) $request->days_requested,
            );

            $causer = Auth::user();
            app(AuditLogger::class)->log(
                'hr.leave-approved',
                $request,
                $causer instanceof User ? $causer : null,
                ['days' => (float) $request->days_requested],
            );

            return $request->refresh();
        });
    }

    public function reject(LeaveRequest $request, string $reason): LeaveRequest
    {
        if (trim($reason) === '') {
            throw ValidationException::withMessages(['reason' => 'A rejection reason is required.']);
        }

        return DB::transaction(function () use ($request, $reason): LeaveRequest {
            $request->status->transitionTo(Rejected::class);
            $request->update(['rejection_reason' => $reason]);

            $balance = $this->balanceFor($request);
            $balance->pending_days = number_format(max(0, (float) $balance->pending_days - (float) $request->days_requested), 2, '.', '');
            $balance->save();

            return $request->refresh();
        });
    }

    public function cancel(LeaveRequest $request): LeaveRequest
    {
        return DB::transaction(function () use ($request): LeaveRequest {
            $wasApproved = (string) $request->status === 'approved';
            $wasSubmitted = (string) $request->status === 'submitted';

            $request->status->transitionTo(Cancelled::class);

            $balance = $this->balanceFor($request);

            if ($wasApproved) {
                $balance->taken_days = number_format(max(0, (float) $balance->taken_days - (float) $request->days_requested), 2, '.', '');
            } elseif ($wasSubmitted) {
                $balance->pending_days = number_format(max(0, (float) $balance->pending_days - (float) $request->days_requested), 2, '.', '');
            }

            $balance->save();

            return $request->refresh();
        });
    }

    /** Overlap against approved + submitted requests (team-calendar warning). */
    public function overlaps(string $employeeId, Carbon $start, Carbon $end): bool
    {
        return LeaveRequest::query()
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['submitted', 'approved'])
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->exists();
    }

    /** Mon–Fri count, public-holiday calendar deferred *(assumed)*. */
    public static function workingDays(Carbon $start, Carbon $end): float
    {
        $days = 0;
        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            if (! $cursor->isWeekend()) {
                $days++;
            }
            $cursor->addDay();
        }

        return (float) $days;
    }

    public function balanceFor(LeaveRequest $request): LeaveBalance
    {
        return LeaveBalance::query()->firstOrCreate(
            [
                'company_id' => $request->company_id,
                'employee_id' => $request->employee_id,
                'leave_type_id' => $request->leave_type_id,
                'year' => $request->start_date->year,
            ],
            ['allocated_days' => 0],
        );
    }

    /**
     * Annual accrual + carry-over for one company/year: allocation =
     * accrual_days_per_year + min(previous remainder, carry_over_days).
     * Idempotent — allocation is recomputed, not incremented.
     */
    public function runAccrual(string $companyId, int $year): int
    {
        $types = LeaveType::query()->where('accrual_days_per_year', '>', 0)->get();
        $employees = Employee::query()->where('status', '!=', 'terminated')->get();
        $updated = 0;

        foreach ($employees as $employee) {
            foreach ($types as $type) {
                $previous = LeaveBalance::query()
                    ->where('employee_id', $employee->id)
                    ->where('leave_type_id', $type->id)
                    ->where('year', $year - 1)
                    ->first();

                $carry = 0.0;
                if ($previous instanceof LeaveBalance) {
                    $remainder = max(0, (float) $previous->allocated_days - (float) $previous->taken_days);
                    $carry = min($remainder, (float) $type->carry_over_days);
                }

                LeaveBalance::query()->updateOrCreate(
                    [
                        'company_id' => $companyId,
                        'employee_id' => $employee->id,
                        'leave_type_id' => $type->id,
                        'year' => $year,
                    ],
                    ['allocated_days' => (float) $type->accrual_days_per_year + $carry],
                );

                $updated++;
            }
        }

        return $updated;
    }
}
