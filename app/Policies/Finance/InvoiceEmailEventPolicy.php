<?php

namespace App\Policies\Finance;

use App\Models\Finance\InvoiceEmailEvent;
use App\Models\Tenant;

class InvoiceEmailEventPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('finance.invoices.view');
    }

    public function view(Tenant $tenant, InvoiceEmailEvent $invoiceEmailEvent): bool
    {
        return $tenant->company_id === $invoiceEmailEvent->company_id
            && $tenant->can('finance.invoices.view');
    }
}
