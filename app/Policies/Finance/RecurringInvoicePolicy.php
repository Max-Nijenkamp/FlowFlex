<?php

namespace App\Policies\Finance;

use App\Models\Finance\RecurringInvoice;
use App\Models\Tenant;

class RecurringInvoicePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.recurring-invoices.view');
    }

    public function view(Tenant $tenant, RecurringInvoice $recurringInvoice): bool
    {
        return $tenant->company_id === $recurringInvoice->company_id
            && $tenant->can('finance.recurring-invoices.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('finance.recurring-invoices.create');
    }

    public function update(Tenant $tenant, RecurringInvoice $recurringInvoice): bool
    {
        return $tenant->company_id === $recurringInvoice->company_id
            && $tenant->can('finance.recurring-invoices.edit');
    }

    public function delete(Tenant $tenant, RecurringInvoice $recurringInvoice): bool
    {
        return $tenant->company_id === $recurringInvoice->company_id
            && $tenant->can('finance.recurring-invoices.delete');
    }

    public function restore(Tenant $tenant, RecurringInvoice $recurringInvoice): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, RecurringInvoice $recurringInvoice): bool
    {
        return false;
    }
}
