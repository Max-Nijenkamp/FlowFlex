<?php

namespace App\Policies\Hr;

use App\Models\Hr\Payslip;
use App\Models\Tenant;

class PayslipPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.view');
    }

    public function view(Tenant $tenant, Payslip $payslip): bool
    {
        return $tenant->company_id === $payslip->company_id
            && ($tenant->can('hr.payroll.view') || $payslip->employee->email === $tenant->email);
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.create');
    }

    public function update(Tenant $tenant, Payslip $payslip): bool
    {
        return $tenant->company_id === $payslip->company_id
            && $tenant->can('hr.payroll.edit');
    }

    public function delete(Tenant $tenant, Payslip $payslip): bool
    {
        return $tenant->company_id === $payslip->company_id
            && $tenant->can('hr.payroll.delete');
    }

    public function restore(Tenant $tenant, Payslip $payslip): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Payslip $payslip): bool
    {
        return false;
    }
}
