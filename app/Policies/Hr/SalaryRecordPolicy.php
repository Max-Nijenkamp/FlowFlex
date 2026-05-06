<?php

namespace App\Policies\Hr;

use App\Models\Hr\SalaryRecord;
use App\Models\Tenant;

class SalaryRecordPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.view');
    }

    public function view(Tenant $tenant, SalaryRecord $record): bool
    {
        return $tenant->company_id === $record->company_id
            && $tenant->can('hr.payroll.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.create');
    }

    public function update(Tenant $tenant, SalaryRecord $record): bool
    {
        return $tenant->company_id === $record->company_id
            && $tenant->can('hr.payroll.edit');
    }

    public function delete(Tenant $tenant, SalaryRecord $record): bool
    {
        return $tenant->company_id === $record->company_id
            && $tenant->can('hr.payroll.delete');
    }

    public function restore(Tenant $tenant, SalaryRecord $record): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, SalaryRecord $record): bool
    {
        return false;
    }
}
