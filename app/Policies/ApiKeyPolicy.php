<?php

namespace App\Policies;

use App\Models\ApiKey;
use App\Models\Tenant;

class ApiKeyPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return true;
    }

    public function view(Tenant $tenant, ApiKey $apiKey): bool
    {
        return $tenant->company_id === $apiKey->company_id;
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('workspace.settings.edit');
    }

    public function update(Tenant $tenant, ApiKey $apiKey): bool
    {
        return $tenant->company_id === $apiKey->company_id
            && $tenant->can('workspace.settings.edit');
    }

    public function delete(Tenant $tenant, ApiKey $apiKey): bool
    {
        return $tenant->company_id === $apiKey->company_id
            && $tenant->can('workspace.settings.edit');
    }

    public function restore(Tenant $tenant, ApiKey $apiKey): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, ApiKey $apiKey): bool
    {
        return false;
    }
}
