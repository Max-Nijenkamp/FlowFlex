<?php

namespace App\Providers;

use App\Events\Hr\LeaveApproved;
use App\Events\Hr\LeaveRejected;
use App\Events\Hr\LeaveRequested;
use App\Events\Hr\OnboardingStarted;
use App\Events\Hr\OnboardingTaskCompleted;
use App\Events\Hr\PayRunProcessed;
use App\Events\Hr\PayslipGenerated;
use App\Events\Projects\TaskAssigned;
use App\Listeners\Hr\NotifyManagerOfLeaveRequest;
use App\Listeners\Hr\NotifyEmployeeLeaveApproved;
use App\Listeners\Hr\NotifyEmployeeLeaveRejected;
use App\Listeners\Hr\DispatchPayslipGenerationJobs;
use App\Listeners\Hr\NotifyEmployeePayslipGenerated;
use App\Listeners\Hr\NotifyEmployeeOnboardingStarted;
use App\Listeners\Hr\NotifyHrOnboardingTaskCompleted;
use App\Listeners\Projects\NotifyAssigneeTaskAssigned;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        LeaveRequested::class => [
            NotifyManagerOfLeaveRequest::class,
        ],
        LeaveApproved::class => [
            NotifyEmployeeLeaveApproved::class,
        ],
        LeaveRejected::class => [
            NotifyEmployeeLeaveRejected::class,
        ],
        PayRunProcessed::class => [
            DispatchPayslipGenerationJobs::class,
        ],
        PayslipGenerated::class => [
            NotifyEmployeePayslipGenerated::class,
        ],
        OnboardingStarted::class => [
            NotifyEmployeeOnboardingStarted::class,
        ],
        OnboardingTaskCompleted::class => [
            NotifyHrOnboardingTaskCompleted::class,
        ],
        TaskAssigned::class => [
            NotifyAssigneeTaskAssigned::class,
        ],
    ];
}
