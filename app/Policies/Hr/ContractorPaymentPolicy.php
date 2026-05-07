<?php

namespace App\Policies\Hr;

use App\Models\Hr\ContractorPayment;
use App\Models\Tenant;

class ContractorPaymentPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.view');
    }

    public function view(Tenant $tenant, ContractorPayment $contractorPayment): bool
    {
        return $tenant->company_id === $contractorPayment->company_id
            && $tenant->can('hr.payroll.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.payroll.create');
    }

    public function update(Tenant $tenant, ContractorPayment $contractorPayment): bool
    {
        return $tenant->company_id === $contractorPayment->company_id
            && $tenant->can('hr.payroll.edit');
    }

    public function delete(Tenant $tenant, ContractorPayment $contractorPayment): bool
    {
        return $tenant->company_id === $contractorPayment->company_id
            && $tenant->can('hr.payroll.delete');
    }

    public function restore(Tenant $tenant, ContractorPayment $contractorPayment): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, ContractorPayment $contractorPayment): bool
    {
        return false;
    }
}
