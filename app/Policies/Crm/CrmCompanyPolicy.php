<?php

namespace App\Policies\Crm;

use App\Models\Crm\CrmCompany;
use App\Models\Tenant;

class CrmCompanyPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.companies.view');
    }

    public function view(Tenant $tenant, CrmCompany $company): bool
    {
        return $tenant->company_id === $company->company_id
            && $tenant->can('crm.companies.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.companies.create');
    }

    public function update(Tenant $tenant, CrmCompany $company): bool
    {
        return $tenant->company_id === $company->company_id
            && $tenant->can('crm.companies.edit');
    }

    public function delete(Tenant $tenant, CrmCompany $company): bool
    {
        return $tenant->company_id === $company->company_id
            && $tenant->can('crm.companies.delete');
    }

    public function restore(Tenant $tenant, CrmCompany $company): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, CrmCompany $company): bool
    {
        return false;
    }
}
