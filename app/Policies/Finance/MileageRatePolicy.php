<?php

namespace App\Policies\Finance;

use App\Models\Finance\MileageRate;
use App\Models\Tenant;

class MileageRatePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.mileage-rates.view');
    }

    public function view(Tenant $tenant, MileageRate $mileageRate): bool
    {
        return $tenant->company_id === $mileageRate->company_id
            && $tenant->can('finance.mileage-rates.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.mileage-rates.create');
    }

    public function update(Tenant $tenant, MileageRate $mileageRate): bool
    {
        return $tenant->company_id === $mileageRate->company_id
            && $tenant->can('finance.mileage-rates.edit');
    }

    public function delete(Tenant $tenant, MileageRate $mileageRate): bool
    {
        return $tenant->company_id === $mileageRate->company_id
            && $tenant->can('finance.mileage-rates.delete');
    }

    public function restore(Tenant $tenant, MileageRate $mileageRate): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, MileageRate $mileageRate): bool
    {
        return false;
    }
}
