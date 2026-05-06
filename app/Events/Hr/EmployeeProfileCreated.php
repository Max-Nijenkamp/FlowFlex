<?php

namespace App\Events\Hr;

use App\Models\Hr\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeProfileCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Employee $employee) {}
}
