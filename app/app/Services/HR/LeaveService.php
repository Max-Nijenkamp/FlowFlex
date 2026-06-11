<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Contracts\HR\LeaveServiceInterface;
use App\Data\HR\SubmitLeaveRequestData;
use App\Events\HR\LeaveRequestApproved;
use App\Exceptions\HR\CannotApproveOwnRequestException;
use App\Exceptions\HR\InsufficientLeaveBalanceException;
use App\Models\HR\Employee;
use App\Models\HR\LeaveBalance;
use App\Models\HR\LeaveRequest;
use App\Models\HR\LeaveType;
use App\States\HR\LeaveRequest\Approved;
use App\States\HR\LeaveRequest\Cancelled;
use App\States\HR\LeaveRequest\Rejected;
use App\States\HR\LeaveRequest\Submitted;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveService implements LeaveServiceInterface
{
    public function submit(SubmitLeaveRequestData $data): LeaveRequest
    {
        $type = LeaveType::query()->findOrFail($data->leave_type_id);
        $start = CarbonImmutable::parse($data->start_date);
        $end = CarbonImmutable::parse($data->end_date);
        $days = $this->calculateWorkingDays($start, $end);

        return DB::transaction(function () use ($data, $type, $start, $days): LeaveRequest {
            $balance = $this->balanceRow($data->employee_id, $type->id, $start->year);

            // Accruing types enforce balance; zero-accrual (unpaid) types don't.
            if ($type->accrual_days_per_year > 0
                && $balance->allocated_days - $balance->taken_days - $balance->pending_days < $days) {
                throw new InsufficientLeaveBalanceException(
                    "Insufficient {$type->name} balance: {$days} days requested."
                );
            }

            $request = LeaveRequest::create([
                'employee_id' => $data->employee_id,
                'leave_type_id' => $type->id,
                'start_date' => $data->start_date,
                'end_date' => $data->end_date,
                'days_requested' => $days,
                'note' => $data->note,
            ]);

            $request->status->transitionTo(Submitted::class);
            $balance->increment('pending_days', $days);

            if (! $type->requires_approval) {
                return $this->doApprove($request->refresh(), approver: null);
            }

            return $request->refresh();
        });
    }

    public function approve(string $leaveRequestId): LeaveRequest
    {
        $request = LeaveRequest::query()->findOrFail($leaveRequestId);

        // Approver may not approve their own request.
        $approverEmployeeId = Employee::query()
            ->where('user_id', Auth::guard('web')->id())
            ->value('id');

        if ($approverEmployeeId !== null && $approverEmployeeId === $request->employee_id) {
            throw new CannotApproveOwnRequestException('You cannot approve your own leave request.');
        }

        return DB::transaction(fn (): LeaveRequest => $this->doApprove($request, Auth::guard('web')->id()));
    }

    public function reject(string $leaveRequestId, string $reason): LeaveRequest
    {
        $request = LeaveRequest::query()->findOrFail($leaveRequestId);

        return DB::transaction(function () use ($request, $reason): LeaveRequest {
            $request->status->transitionTo(Rejected::class);
            $request->forceFill(['rejection_reason' => $reason])->save();
            $this->releasePending($request);

            return $request->refresh();
        });
    }

    public function cancel(string $leaveRequestId): LeaveRequest
    {
        $request = LeaveRequest::query()->findOrFail($leaveRequestId);

        return DB::transaction(function () use ($request): LeaveRequest {
            $wasApproved = $request->status->equals(Approved::class);
            $request->status->transitionTo(Cancelled::class);

            $balance = $this->balanceRow($request->employee_id, $request->leave_type_id, $request->start_date->year);
            $wasApproved
                ? $balance->decrement('taken_days', $request->days_requested)
                : $balance->decrement('pending_days', $request->days_requested);

            return $request->refresh();
        });
    }

    public function balanceFor(string $employeeId, int $year): Collection
    {
        return LeaveBalance::query()
            ->where('employee_id', $employeeId)
            ->where('year', $year)
            ->get();
    }

    public function calculateWorkingDays(CarbonImmutable $start, CarbonImmutable $end): float
    {
        $days = 0;
        for ($d = $start; $d->lessThanOrEqualTo($end); $d = $d->addDay()) {
            if (! $d->isWeekend()) {
                $days++;
            }
        }

        return (float) $days;
    }

    public function accrueMonthly(): void
    {
        $year = now()->year;

        LeaveType::query()->where('accrual_days_per_year', '>', 0)->get()
            ->each(function (LeaveType $type) use ($year): void {
                Employee::query()->where('status', 'active')->pluck('id')
                    ->each(function (string $employeeId) use ($type, $year): void {
                        $this->balanceRow($employeeId, $type->id, $year)
                            ->increment('allocated_days', round($type->accrual_days_per_year / 12, 2));
                    });
            });
    }

    private function doApprove(LeaveRequest $request, ?string $approver): LeaveRequest
    {
        $request->status->transitionTo(Approved::class);
        $request->forceFill(['approved_by' => $approver, 'approved_at' => now()])->save();

        $balance = $this->balanceRow($request->employee_id, $request->leave_type_id, $request->start_date->year);
        $balance->decrement('pending_days', $request->days_requested);
        $balance->increment('taken_days', $request->days_requested);

        event(new LeaveRequestApproved(
            company_id: $request->company_id,
            leave_request_id: $request->id,
            employee_id: $request->employee_id,
            leave_type_id: $request->leave_type_id,
            start_date: CarbonImmutable::parse($request->start_date),
            end_date: CarbonImmutable::parse($request->end_date),
            days: $request->days_requested,
        ));

        return $request->refresh();
    }

    private function releasePending(LeaveRequest $request): void
    {
        $this->balanceRow($request->employee_id, $request->leave_type_id, $request->start_date->year)
            ->decrement('pending_days', $request->days_requested);
    }

    private function balanceRow(string $employeeId, string $leaveTypeId, int $year): LeaveBalance
    {
        return LeaveBalance::query()->firstOrCreate(
            ['employee_id' => $employeeId, 'leave_type_id' => $leaveTypeId, 'year' => $year],
        );
    }
}
