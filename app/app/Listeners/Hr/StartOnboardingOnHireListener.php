<?php

declare(strict_types=1);

namespace App\Listeners\Hr;

use App\Events\Hr\EmployeeHired;
use App\Mail\Hr\WelcomeMail;
use App\Models\Hr\Employee;
use App\Models\Hr\OnboardingPlan;
use App\Services\BillingService;
use App\Services\Hr\OnboardingService;
use App\Support\Jobs\Middleware\WithCompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

/**
 * EmployeeHired → onboarding plan from the matching template + welcome
 * email (hr.onboarding/plan-generation-on-hire + welcome-email). No-op
 * while hr.onboarding is inactive for the company.
 */
class StartOnboardingOnHireListener implements ShouldQueue
{
    public string $queue = 'domain-events';

    public function handle(EmployeeHired $event): void
    {
        WithCompanyContext::restore($event->company_id);

        if (! app(BillingService::class)->hasModule('hr.onboarding')) {
            return;
        }

        $employee = Employee::query()->find($event->employee_id);

        if (! $employee instanceof Employee) {
            return;
        }

        $plan = app(OnboardingService::class)->generatePlan($employee);

        if ($plan instanceof OnboardingPlan) {
            Mail::to($employee->email)->queue(new WelcomeMail($employee->id));
        }
    }
}
