<?php

namespace App\Policies\Hr;

use App\Models\Hr\Employee;
use App\Models\Tenant;

class EmployeePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.employees.view');
    }

    public function view(Tenant $tenant, Employee $employee): bool
    {
        return $tenant->company_id === $employee->company_id
            && $tenant->can('hr.employees.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.employees.create');
    }

    public function update(Tenant $tenant, Employee $employee): bool
    {
        return $tenant->company_id === $employee->company_id
            && $tenant->can('hr.employees.edit');
    }

    public function delete(Tenant $tenant, Employee $employee): bool
    {
        return $tenant->company_id === $employee->company_id
            && $tenant->can('hr.employees.delete');
    }

    public function restore(Tenant $tenant, Employee $employee): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Employee $employee): bool
    {
        return false;
    }
}
