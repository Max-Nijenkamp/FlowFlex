<?php

namespace App\Policies\Crm;

use App\Models\Crm\DealStage;
use App\Models\Tenant;

class DealStagePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.deal-stages.view');
    }

    public function view(Tenant $tenant, DealStage $dealStage): bool
    {
        return $tenant->company_id === $dealStage->company_id
            && $tenant->can('crm.deal-stages.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.deal-stages.create');
    }

    public function update(Tenant $tenant, DealStage $dealStage): bool
    {
        return $tenant->company_id === $dealStage->company_id
            && $tenant->can('crm.deal-stages.edit');
    }

    public function delete(Tenant $tenant, DealStage $dealStage): bool
    {
        return $tenant->company_id === $dealStage->company_id
            && $tenant->can('crm.deal-stages.delete');
    }

    public function restore(Tenant $tenant, DealStage $dealStage): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, DealStage $dealStage): bool
    {
        return false;
    }
}
