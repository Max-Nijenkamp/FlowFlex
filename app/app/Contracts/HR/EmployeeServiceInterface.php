<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Data\HR\CreateEmployeeData;
use App\Data\HR\UpdateEmployeeData;
use App\Models\Company;
use App\Models\HR\Employee;

interface EmployeeServiceInterface
{
    public function create(CreateEmployeeData $data, Company $company): Employee;

    public function update(Employee $employee, UpdateEmployeeData $data): Employee;

    public function terminate(Employee $employee, ?string $terminationDate = null): Employee;
}
