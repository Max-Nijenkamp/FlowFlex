<?php

namespace App\Events\Hr;

use App\Models\Hr\PayRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayRunCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly PayRun $payRun) {}
}
