<?php

namespace App\Policies\Crm;

use App\Models\Crm\Ticket;
use App\Models\Tenant;

class TicketPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.tickets.view');
    }

    public function view(Tenant $tenant, Ticket $ticket): bool
    {
        return $tenant->company_id === $ticket->company_id
            && $tenant->can('crm.tickets.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.tickets.create');
    }

    public function update(Tenant $tenant, Ticket $ticket): bool
    {
        return $tenant->company_id === $ticket->company_id
            && $tenant->can('crm.tickets.edit');
    }

    public function delete(Tenant $tenant, Ticket $ticket): bool
    {
        return $tenant->company_id === $ticket->company_id
            && $tenant->can('crm.tickets.delete');
    }

    public function resolve(Tenant $tenant, Ticket $ticket): bool
    {
        return $tenant->company_id === $ticket->company_id
            && $tenant->can('crm.tickets.resolve');
    }

    public function restore(Tenant $tenant, Ticket $ticket): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Ticket $ticket): bool
    {
        return false;
    }
}
