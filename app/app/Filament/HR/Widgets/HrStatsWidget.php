<?php

declare(strict_types=1);

namespace App\Filament\HR\Widgets;

use App\Models\HR\Employee;
use App\Models\HR\JobRequisition;
use App\Models\HR\LeaveRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class HrStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.profiles.view-any');
    }

    protected function getStats(): array
    {
        $headcount = Employee::query()->where('status', 'active')->count();

        $onLeaveToday = LeaveRequest::query()
            ->where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->distinct('employee_id')
            ->count('employee_id');

        $pendingLeave = LeaveRequest::query()->where('status', 'submitted')->count();

        $openRoles = JobRequisition::query()->where('status', 'open')->count();

        $hiredThisQuarter = Employee::query()
            ->where('hire_date', '>=', now()->startOfQuarter())
            ->count();

        return [
            Stat::make('Headcount', (string) $headcount)
                ->description($hiredThisQuarter.' hired this quarter')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('On leave today', (string) $onLeaveToday)
                ->description('Approved leave covering today'),
            Stat::make('Pending leave requests', (string) $pendingLeave)
                ->description($pendingLeave > 0 ? 'Waiting for approval' : 'All caught up')
                ->color($pendingLeave > 0 ? 'warning' : 'success'),
            Stat::make('Open roles', (string) $openRoles)
                ->description('Published requisitions'),
        ];
    }
}
