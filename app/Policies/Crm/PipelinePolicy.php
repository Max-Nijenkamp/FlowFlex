<?php

namespace App\Policies\Crm;

use App\Models\Crm\Pipeline;
use App\Models\Tenant;

class PipelinePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('crm.pipelines.view');
    }

    public function view(Tenant $tenant, Pipeline $pipeline): bool
    {
        return $tenant->company_id === $pipeline->company_id
            && $tenant->can('crm.pipelines.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('crm.pipelines.create');
    }

    public function update(Tenant $tenant, Pipeline $pipeline): bool
    {
        return $tenant->company_id === $pipeline->company_id
            && $tenant->can('crm.pipelines.edit');
    }

    public function delete(Tenant $tenant, Pipeline $pipeline): bool
    {
        return $tenant->company_id === $pipeline->company_id
            && $tenant->can('crm.pipelines.delete');
    }

    public function restore(Tenant $tenant, Pipeline $pipeline): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Pipeline $pipeline): bool
    {
        return false;
    }
}
