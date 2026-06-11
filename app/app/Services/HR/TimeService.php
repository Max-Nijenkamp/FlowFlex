<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Events\HR\TimesheetApproved;
use App\Exceptions\HR\AlreadyClockedInException;
use App\Exceptions\HR\CannotApproveOwnRequestException;
use App\Models\HR\Employee;
use App\Models\HR\TimeEntry;
use App\Models\HR\Timesheet;
use App\States\HR\Timesheet\Approved;
use App\States\HR\Timesheet\Rejected;
use App\States\HR\Timesheet\Submitted;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TimeService
{
    public function clockIn(string $employeeId): TimeEntry
    {
        $open = TimeEntry::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', now()->toDateString())
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->exists();

        if ($open) {
            throw new AlreadyClockedInException('Already clocked in today.');
        }

        return TimeEntry::query()->updateOrCreate(
            ['employee_id' => $employeeId, 'date' => now()->toDateString()],
            ['clock_in' => now()->format('H:i:s')],
        );
    }

    public function clockOut(string $employeeId): TimeEntry
    {
        $entry = TimeEntry::query()
            ->where('employee_id', $employeeId)
            ->whereDate('date', now()->toDateString())
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->firstOrFail();

        $out = now();
        $in = now()->setTimeFromTimeString($entry->clock_in);
        $minutes = max(0, (int) $in->diffInMinutes($out) - $entry->break_minutes);

        $entry->update([
            'clock_out' => $out->format('H:i:s'),
            'total_minutes' => $minutes,
            'is_overtime' => $minutes > 8 * 60,
        ]);

        return $entry->refresh();
    }

    /** Bundles the week's entries into a timesheet and submits it. */
    public function submitWeek(string $employeeId, string $weekStart): Timesheet
    {
        return DB::transaction(function () use ($employeeId, $weekStart): Timesheet {
            $start = CarbonImmutable::parse($weekStart);

            $timesheet = Timesheet::query()->firstOrCreate(
                ['employee_id' => $employeeId, 'week_start' => $start->toDateString()],
            );

            $entries = TimeEntry::query()
                ->where('employee_id', $employeeId)
                ->whereBetween('date', [$start, $start->addDays(6)])
                ->get();

            $entries->each->update(['timesheet_id' => $timesheet->id]);
            $timesheet->update(['total_minutes' => (int) $entries->sum('total_minutes')]);
            $timesheet->status->transitionTo(Submitted::class);

            return $timesheet->refresh();
        });
    }

    public function approve(string $timesheetId): Timesheet
    {
        $timesheet = Timesheet::query()->findOrFail($timesheetId);

        $approverEmployeeId = Employee::query()->where('user_id', Auth::guard('web')->id())->value('id');
        if ($approverEmployeeId !== null && $approverEmployeeId === $timesheet->employee_id) {
            throw new CannotApproveOwnRequestException('You cannot approve your own timesheet.');
        }

        $timesheet->status->transitionTo(Approved::class);
        $timesheet->forceFill(['approved_by' => Auth::guard('web')->id(), 'approved_at' => now()])->save();

        $weekStart = CarbonImmutable::parse($timesheet->week_start);
        event(new TimesheetApproved(
            company_id: $timesheet->company_id,
            timesheet_id: $timesheet->id,
            employee_id: $timesheet->employee_id,
            period_start: $weekStart,
            period_end: $weekStart->addDays(6),
            total_minutes: $timesheet->total_minutes,
        ));

        return $timesheet->refresh();
    }

    public function reject(string $timesheetId, string $note): Timesheet
    {
        $timesheet = Timesheet::query()->findOrFail($timesheetId);
        $timesheet->status->transitionTo(Rejected::class);
        $timesheet->entries()->update(['timesheet_id' => null]); // unlock

        return $timesheet->refresh();
    }
}
