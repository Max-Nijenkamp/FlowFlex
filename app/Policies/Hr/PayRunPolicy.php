<?php

namespace App\Policies\Hr;

use App\Models\Hr\PayRun;
use App\Models\Tenant;

class PayRunPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.view');
    }

    public function view(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.payroll.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.create');
    }

    public function update(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.payroll.edit');
    }

    public function delete(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.payroll.delete');
    }

    public function process(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.payroll.run');
    }

    public function restore(Tenant $tenant, PayRun $payRun): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, PayRun $payRun): bool
    {
        return false;
    }
}
