<?php

namespace App\Policies\Finance;

use App\Models\Finance\InvoiceLine;
use App\Models\Tenant;

class InvoiceLinePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.view');
    }

    public function view(Tenant $tenant, InvoiceLine $invoiceLine): bool
    {
        return $tenant->company_id === $invoiceLine->company_id
            && $tenant->can('finance.invoices.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.create');
    }

    public function update(Tenant $tenant, InvoiceLine $invoiceLine): bool
    {
        return $tenant->company_id === $invoiceLine->company_id
            && $tenant->can('finance.invoices.edit');
    }

    public function delete(Tenant $tenant, InvoiceLine $invoiceLine): bool
    {
        return $tenant->company_id === $invoiceLine->company_id
            && $tenant->can('finance.invoices.delete');
    }

    public function restore(Tenant $tenant, InvoiceLine $invoiceLine): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, InvoiceLine $invoiceLine): bool
    {
        return false;
    }
}
