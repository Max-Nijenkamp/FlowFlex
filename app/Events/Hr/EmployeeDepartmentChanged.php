<?php

namespace App\Events\Hr;

use App\Models\Hr\Department;
use App\Models\Hr\Employee;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmployeeDepartmentChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Employee    $employee,
        public readonly ?Department $oldDepartment,
        public readonly Department  $newDepartment,
    ) {}
}
