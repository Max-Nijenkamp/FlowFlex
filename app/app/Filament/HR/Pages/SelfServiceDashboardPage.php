<?php

declare(strict_types=1);

namespace App\Filament\HR\Pages;

use App\Contracts\BillingServiceInterface;
use App\Models\HR\Employee;
use App\Models\HR\LeaveBalance;
use App\Models\HR\OnboardingPlan;
use App\Models\HR\OnboardingPlanTask;
use App\Models\HR\Payslip;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

/**
 * "My HR" dashboard (ui-strategy row #6). Soft-dep tiles render only when
 * their module is active. Own-data rule: everything scoped to own employee.
 */
class SelfServiceDashboardPage extends Page
{
    protected string $view = 'filament.hr.pages.self-service-dashboard';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static string|UnitEnum|null $navigationGroup = 'My HR';

    protected static ?string $title = 'My HR';

    public static function canAccess(): bool
    {
        return Auth::guard('web')->check()
            && Auth::guard('web')->user()->can('hr.self-service.view')
            && app(BillingServiceInterface::class)->hasModule('hr.self-service');
    }

    /** @return array<string, mixed> */
    public function getTiles(): array
    {
        $billing = app(BillingServiceInterface::class);
        $employee = Employee::query()->where('user_id', Auth::guard('web')->id())->first();

        if ($employee === null) {
            return ['employee' => null];
        }

        $tiles = ['employee' => $employee];

        if ($billing->hasModule('hr.leave')) {
            $tiles['leave_remaining'] = LeaveBalance::query()
                ->where('employee_id', $employee->id)
                ->where('year', now()->year)
                ->get()
                ->sum(fn (LeaveBalance $b) => $b->remaining_days);
        }

        if ($billing->hasModule('hr.payroll')) {
            $tiles['last_payslip'] = Payslip::query()
                ->where('employee_id', $employee->id)
                ->latest()
                ->first();
        }

        if ($billing->hasModule('hr.onboarding')) {
            $tiles['open_tasks'] = OnboardingPlanTask::query()
                ->whereHas('task', fn ($q) => $q->where('assigned_role', 'employee'))
                ->where('status', 'pending')
                ->whereIn('plan_id', $employee->id !== null
                    ? OnboardingPlan::query()->where('employee_id', $employee->id)->pluck('id')
                    : [])
                ->count();
        }

        return $tiles;
    }
}
