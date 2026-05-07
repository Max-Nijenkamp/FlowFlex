<?php

namespace App\Listeners\Finance;

use App\Events\Finance\InvoiceOverdue;
use App\Models\Tenant;
use App\Notifications\Finance\InvoiceOverdueNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyInvoiceOverdue implements ShouldQueue
{
    public function handle(InvoiceOverdue $event): void
    {
        $invoice = $event->invoice;

        // Notify all tenants in the company who have finance permissions
        Tenant::where('company_id', $invoice->company_id)
            ->get()
            ->filter(fn (Tenant $tenant) => $tenant->can('finance.invoices.view'))
            ->each(fn (Tenant $tenant) => $tenant->notify(new InvoiceOverdueNotification($invoice)));
    }
}
