<?php

namespace App\Events\Hr;

use App\Models\Hr\OnboardingFlow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OnboardingCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly OnboardingFlow $flow) {}
}
