<?php

namespace App\Policies\Hr;

use App\Models\Hr\PayRun;
use App\Models\Tenant;

class PayRunPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.pay-runs.view');
    }

    public function view(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.pay-runs.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.pay-runs.create');
    }

    public function update(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.pay-runs.edit');
    }

    public function delete(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.pay-runs.delete');
    }

    public function process(Tenant $tenant, PayRun $payRun): bool
    {
        return $tenant->company_id === $payRun->company_id
            && $tenant->can('hr.pay-runs.run');
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
