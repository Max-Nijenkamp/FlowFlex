<?php

declare(strict_types=1);

namespace App\Contracts\Hr;

use App\Data\Hr\CreateEmployeeData;
use App\Data\Hr\OffboardEmployeeData;
use App\Models\Hr\Employee;

interface EmployeeServiceInterface
{
    public function hire(CreateEmployeeData $data): Employee;

    public function changeManager(string $employeeId, ?string $managerId): Employee;

    public function offboard(OffboardEmployeeData $data): Employee;
}
