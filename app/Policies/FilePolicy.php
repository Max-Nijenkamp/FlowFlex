<?php

namespace App\Policies;

use App\Models\File;
use App\Models\Tenant;

class FilePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return true;
    }

    public function view(Tenant $tenant, File $file): bool
    {
        return $tenant->company_id === $file->company_id;
    }

    public function create(Tenant $tenant): bool
    {
        return true;
    }

    public function update(Tenant $tenant, File $file): bool
    {
        return $tenant->company_id === $file->company_id;
    }

    public function delete(Tenant $tenant, File $file): bool
    {
        return $tenant->company_id === $file->company_id;
    }

    public function restore(Tenant $tenant, File $file): bool
    {
        return $tenant->company_id === $file->company_id;
    }

    public function forceDelete(Tenant $tenant, File $file): bool
    {
        return false;
    }
}
