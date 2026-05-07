<?php

namespace App\Policies\Crm;

use App\Models\Crm\TicketSlaRule;
use App\Models\Tenant;

class TicketSlaRulePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.ticket-sla-rules.view');
    }

    public function view(Tenant $tenant, TicketSlaRule $ticketSlaRule): bool
    {
        return $tenant->company_id === $ticketSlaRule->company_id
            && $tenant->can('crm.ticket-sla-rules.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.ticket-sla-rules.create');
    }

    public function update(Tenant $tenant, TicketSlaRule $ticketSlaRule): bool
    {
        return $tenant->company_id === $ticketSlaRule->company_id
            && $tenant->can('crm.ticket-sla-rules.edit');
    }

    public function delete(Tenant $tenant, TicketSlaRule $ticketSlaRule): bool
    {
        return $tenant->company_id === $ticketSlaRule->company_id
            && $tenant->can('crm.ticket-sla-rules.delete');
    }

    public function restore(Tenant $tenant, TicketSlaRule $ticketSlaRule): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, TicketSlaRule $ticketSlaRule): bool
    {
        return false;
    }
}
