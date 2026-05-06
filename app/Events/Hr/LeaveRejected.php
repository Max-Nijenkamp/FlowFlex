<?php

namespace App\Events\Hr;

use App\Models\Hr\LeaveRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveRejected
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly LeaveRequest $request) {}
}
