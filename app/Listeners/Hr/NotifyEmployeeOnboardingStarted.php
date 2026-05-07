<?php

namespace App\Listeners\Hr;

use App\Events\Hr\OnboardingStarted;
use App\Models\Tenant;
use App\Notifications\Hr\OnboardingStartedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyEmployeeOnboardingStarted implements ShouldQueue
{
    public function handle(OnboardingStarted $event): void
    {
        $flow = $event->flow;
        $employee = $flow->employee;

        if (! $employee || ! $employee->email) {
            return;
        }

        $tenant = Tenant::where('company_id', $flow->company_id)
            ->where('email', $employee->email)
            ->first();

        if ($tenant) {
            $tenant->notify(new OnboardingStartedNotification($flow));
        }
    }
}
