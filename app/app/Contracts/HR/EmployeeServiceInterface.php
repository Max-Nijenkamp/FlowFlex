<?php

declare(strict_types=1);

namespace App\Contracts\HR;

use App\Data\HR\CreateEmployeeData;
use App\Data\HR\OffboardEmployeeData;
use App\Models\HR\Employee;
use Illuminate\Support\Collection;

interface EmployeeServiceInterface
{
    /** Assigns the next sequential employee number, fires EmployeeHired. */
    public function hire(CreateEmployeeData $data): Employee;

    /** Transitions to terminated, fires EmployeeOffboarded. */
    public function offboard(OffboardEmployeeData $data): Employee;

    /** @return Collection<int, Employee> */
    public function directReports(string $employeeId): Collection;

    /** Throws ManagerCycleException if the assignment creates a cycle. */
    public function assignManager(string $employeeId, ?string $managerId): Employee;
}
