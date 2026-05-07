<?php

namespace App\Policies\Crm;

use App\Models\Crm\CrmContact;
use App\Models\Tenant;

class CrmContactPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.contacts.view');
    }

    public function view(Tenant $tenant, CrmContact $contact): bool
    {
        return $tenant->company_id === $contact->company_id
            && $tenant->can('crm.contacts.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.contacts.create');
    }

    public function update(Tenant $tenant, CrmContact $contact): bool
    {
        return $tenant->company_id === $contact->company_id
            && $tenant->can('crm.contacts.edit');
    }

    public function delete(Tenant $tenant, CrmContact $contact): bool
    {
        return $tenant->company_id === $contact->company_id
            && $tenant->can('crm.contacts.delete');
    }

    public function restore(Tenant $tenant, CrmContact $contact): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, CrmContact $contact): bool
    {
        return false;
    }
}
