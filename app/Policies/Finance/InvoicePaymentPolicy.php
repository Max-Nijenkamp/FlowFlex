<?php

namespace App\Policies\Finance;

use App\Models\Finance\InvoicePayment;
use App\Models\Tenant;

class InvoicePaymentPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.view');
    }

    public function view(Tenant $tenant, InvoicePayment $invoicePayment): bool
    {
        return $tenant->company_id === $invoicePayment->company_id
            && $tenant->can('finance.invoices.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.create');
    }

    public function update(Tenant $tenant, InvoicePayment $invoicePayment): bool
    {
        return $tenant->company_id === $invoicePayment->company_id
            && $tenant->can('finance.invoices.edit');
    }

    public function delete(Tenant $tenant, InvoicePayment $invoicePayment): bool
    {
        return $tenant->company_id === $invoicePayment->company_id
            && $tenant->can('finance.invoices.delete');
    }

    public function restore(Tenant $tenant, InvoicePayment $invoicePayment): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, InvoicePayment $invoicePayment): bool
    {
        return false;
    }
}
