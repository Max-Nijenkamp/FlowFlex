<?php

namespace App\Events\Hr;

use App\Models\Hr\LeaveBalance;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveBalanceLow
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly LeaveBalance $balance) {}
}
