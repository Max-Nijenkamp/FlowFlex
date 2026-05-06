<?php

namespace App\Events\Hr;

use App\Models\Hr\PayRun;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PayRunProcessed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly PayRun $payRun) {}
}
