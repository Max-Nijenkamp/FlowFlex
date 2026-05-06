<?php

namespace App\Events\Hr;

use App\Models\Hr\OnboardingTask;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnboardingTaskCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly OnboardingTask $task) {}
}
