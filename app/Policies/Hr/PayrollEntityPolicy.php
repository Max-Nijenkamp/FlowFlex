<?php

namespace App\Policies\Hr;

use App\Models\Hr\PayrollEntity;
use App\Models\Tenant;

class PayrollEntityPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.view');
    }

    public function view(Tenant $tenant, PayrollEntity $entity): bool
    {
        return $tenant->company_id === $entity->company_id
            && $tenant->can('hr.payroll.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.create');
    }

    public function update(Tenant $tenant, PayrollEntity $entity): bool
    {
        return $tenant->company_id === $entity->company_id
            && $tenant->can('hr.payroll.edit');
    }

    public function delete(Tenant $tenant, PayrollEntity $entity): bool
    {
        return $tenant->company_id === $entity->company_id
            && $tenant->can('hr.payroll.delete');
    }

    public function restore(Tenant $tenant, PayrollEntity $entity): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, PayrollEntity $entity): bool
    {
        return false;
    }
}
