<?php

namespace App\Policies\Hr;

use App\Models\Hr\Deduction;
use App\Models\Tenant;

class DeductionPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.view');
    }

    public function view(Tenant $tenant, Deduction $deduction): bool
    {
        return $tenant->company_id === $deduction->company_id
            && $tenant->can('hr.payroll.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.create');
    }

    public function update(Tenant $tenant, Deduction $deduction): bool
    {
        return $tenant->company_id === $deduction->company_id
            && $tenant->can('hr.payroll.edit');
    }

    public function delete(Tenant $tenant, Deduction $deduction): bool
    {
        return $tenant->company_id === $deduction->company_id
            && $tenant->can('hr.payroll.delete');
    }

    public function restore(Tenant $tenant, Deduction $deduction): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Deduction $deduction): bool
    {
        return false;
    }
}
