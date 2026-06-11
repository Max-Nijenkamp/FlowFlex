<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Exceptions\HR\EmployeeOnLeaveException;
use App\Exceptions\HR\ShiftConflictException;
use App\Models\HR\LeaveRequest;
use App\Models\HR\Shift;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class ShiftService
{
    public function createShift(
        string $date,
        string $startTime,
        string $endTime,
        string $role,
        ?string $employeeId = null,
    ): Shift {
        if ($employeeId !== null) {
            $conflict = Shift::query()
                ->where('employee_id', $employeeId)
                ->whereDate('date', $date)
                ->where('status', '!=', 'cancelled')
                ->where(fn ($q) => $q->where('start_time', '<', $endTime)->where('end_time', '>', $startTime))
                ->exists();

            if ($conflict) {
                throw new ShiftConflictException('Employee already has an overlapping shift.');
            }

            $onLeave = LeaveRequest::query()
                ->where('employee_id', $employeeId)
                ->where('status', 'approved')
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->exists();

            if ($onLeave) {
                throw new EmployeeOnLeaveException('Employee has approved leave on this date.');
            }
        }

        return Shift::create([
            'employee_id' => $employeeId,
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'role' => $role,
        ]);
    }

    public function publishWeek(CarbonImmutable $weekStart): int
    {
        return Shift::query()
            ->whereBetween('date', [$weekStart, $weekStart->addDays(6)])
            ->where('status', 'draft')
            ->update(['status' => 'published']);
    }

    /** @return Collection<int, Shift> unassigned shifts = coverage gaps */
    public function coverageGaps(CarbonImmutable $weekStart): Collection
    {
        return Shift::query()
            ->whereBetween('date', [$weekStart, $weekStart->addDays(6)])
            ->whereNull('employee_id')
            ->where('status', '!=', 'cancelled')
            ->get();
    }
}
