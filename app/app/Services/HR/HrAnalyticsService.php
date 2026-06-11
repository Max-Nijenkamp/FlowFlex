<?php

declare(strict_types=1);

namespace App\Services\HR;

use App\Models\HR\Employee;
use App\Models\HR\LeaveRequest;
use Carbon\CarbonImmutable;

class HrAnalyticsService
{
    /**
     * All aggregates in single queries — no N+1.
     *
     * @return array<string, mixed>
     */
    public function metrics(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $headcount = Employee::query()->where('status', '!=', 'terminated')->count();

        $hires = Employee::query()->whereBetween('hire_date', [$from, $to])->count();

        $terminations = Employee::query()
            ->whereBetween('termination_date', [$from, $to])
            ->count();

        $turnoverRate = $headcount > 0 ? round($terminations / $headcount * 100, 1) : 0.0;

        $leaveDays = (float) LeaveRequest::query()
            ->where('status', 'approved')
            ->whereBetween('start_date', [$from, $to])
            ->sum('days_requested');

        $byDepartment = Employee::query()
            ->where('status', '!=', 'terminated')
            ->selectRaw('department_id, COUNT(*) as count')
            ->groupBy('department_id')
            ->pluck('count', 'department_id')
            ->all();

        return [
            'headcount' => $headcount,
            'hires' => $hires,
            'terminations' => $terminations,
            'turnover_rate_percent' => $turnoverRate,
            'approved_leave_days' => $leaveDays,
            'headcount_by_department' => $byDepartment,
        ];
    }
}
