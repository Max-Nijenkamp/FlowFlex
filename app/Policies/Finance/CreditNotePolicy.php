<?php

namespace App\Policies\Finance;

use App\Models\Finance\CreditNote;
use App\Models\Tenant;

class CreditNotePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.credit-notes.view');
    }

    public function view(Tenant $tenant, CreditNote $creditNote): bool
    {
        return $tenant->company_id === $creditNote->company_id
            && $tenant->can('finance.credit-notes.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.credit-notes.create');
    }

    public function update(Tenant $tenant, CreditNote $creditNote): bool
    {
        return $tenant->company_id === $creditNote->company_id
            && $tenant->can('finance.credit-notes.edit');
    }

    public function delete(Tenant $tenant, CreditNote $creditNote): bool
    {
        return $tenant->company_id === $creditNote->company_id
            && $tenant->can('finance.credit-notes.delete');
    }

    public function restore(Tenant $tenant, CreditNote $creditNote): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, CreditNote $creditNote): bool
    {
        return false;
    }
}
