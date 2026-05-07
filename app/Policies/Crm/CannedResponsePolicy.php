<?php

namespace App\Policies\Crm;

use App\Models\Crm\CannedResponse;
use App\Models\Tenant;

class CannedResponsePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.canned-responses.view');
    }

    public function view(Tenant $tenant, CannedResponse $cannedResponse): bool
    {
        return $tenant->company_id === $cannedResponse->company_id
            && $tenant->can('crm.canned-responses.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.canned-responses.create');
    }

    public function update(Tenant $tenant, CannedResponse $cannedResponse): bool
    {
        return $tenant->company_id === $cannedResponse->company_id
            && $tenant->can('crm.canned-responses.edit');
    }

    public function delete(Tenant $tenant, CannedResponse $cannedResponse): bool
    {
        return $tenant->company_id === $cannedResponse->company_id
            && $tenant->can('crm.canned-responses.delete');
    }

    public function restore(Tenant $tenant, CannedResponse $cannedResponse): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, CannedResponse $cannedResponse): bool
    {
        return false;
    }
}
