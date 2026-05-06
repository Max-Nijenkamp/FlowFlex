<?php

namespace App\Policies\Hr;

use App\Models\Hr\Department;
use App\Models\Tenant;

class DepartmentPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.employees.view');
    }

    public function view(Tenant $tenant, Department $department): bool
    {
        return $tenant->company_id === $department->company_id
            && $tenant->can('hr.employees.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.employees.create');
    }

    public function update(Tenant $tenant, Department $department): bool
    {
        return $tenant->company_id === $department->company_id
            && $tenant->can('hr.employees.edit');
    }

    public function delete(Tenant $tenant, Department $department): bool
    {
        return $tenant->company_id === $department->company_id
            && $tenant->can('hr.employees.delete');
    }

    public function restore(Tenant $tenant, Department $department): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Department $department): bool
    {
        return false;
    }
}
