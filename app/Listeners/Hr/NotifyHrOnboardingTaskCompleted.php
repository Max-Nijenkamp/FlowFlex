<?php

namespace App\Listeners\Hr;

use App\Events\Hr\OnboardingTaskCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyHrOnboardingTaskCompleted implements ShouldQueue
{
    public function handle(OnboardingTaskCompleted $event): void
    {
        // Future: notify HR team when an onboarding task is completed.
        // For now this is a placeholder — no action taken.
        return;
    }
}
