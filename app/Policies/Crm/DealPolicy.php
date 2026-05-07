<?php

namespace App\Policies\Crm;

use App\Models\Crm\Deal;
use App\Models\Tenant;

class DealPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.deals.view');
    }

    public function view(Tenant $tenant, Deal $deal): bool
    {
        return $tenant->company_id === $deal->company_id
            && $tenant->can('crm.deals.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.deals.create');
    }

    public function update(Tenant $tenant, Deal $deal): bool
    {
        return $tenant->company_id === $deal->company_id
            && $tenant->can('crm.deals.edit');
    }

    public function delete(Tenant $tenant, Deal $deal): bool
    {
        return $tenant->company_id === $deal->company_id
            && $tenant->can('crm.deals.delete');
    }

    public function restore(Tenant $tenant, Deal $deal): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Deal $deal): bool
    {
        return false;
    }
}
