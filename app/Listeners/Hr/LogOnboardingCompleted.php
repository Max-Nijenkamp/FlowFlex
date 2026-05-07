<?php

namespace App\Listeners\Hr;

use App\Events\Hr\OnboardingCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogOnboardingCompleted implements ShouldQueue
{
    public function handle(OnboardingCompleted $event): void
    {
        // LogsActivity trait handles auditing. Add integrations, webhooks, or analytics here.
    }
}
