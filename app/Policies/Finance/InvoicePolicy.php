<?php

namespace App\Policies\Finance;

use App\Models\Finance\Invoice;
use App\Models\Tenant;

class InvoicePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.view');
    }

    public function view(Tenant $tenant, Invoice $invoice): bool
    {
        return $tenant->company_id === $invoice->company_id
            && $tenant->can('finance.invoices.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.create');
    }

    public function update(Tenant $tenant, Invoice $invoice): bool
    {
        return $tenant->company_id === $invoice->company_id
            && $tenant->can('finance.invoices.edit');
    }

    public function delete(Tenant $tenant, Invoice $invoice): bool
    {
        return $tenant->company_id === $invoice->company_id
            && $tenant->can('finance.invoices.delete');
    }

    public function restore(Tenant $tenant, Invoice $invoice): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Invoice $invoice): bool
    {
        return false;
    }
}
