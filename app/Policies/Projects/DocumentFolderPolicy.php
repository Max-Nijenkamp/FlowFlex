<?php

namespace App\Policies\Projects;

use App\Models\Projects\DocumentFolder;
use App\Models\Tenant;

class DocumentFolderPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.documents.view');
    }

    public function view(Tenant $tenant, DocumentFolder $documentFolder): bool
    {
        return $tenant->company_id === $documentFolder->company_id
            && $tenant->can('projects.documents.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.documents.create');
    }

    public function update(Tenant $tenant, DocumentFolder $documentFolder): bool
    {
        return $tenant->company_id === $documentFolder->company_id
            && $tenant->can('projects.documents.edit');
    }

    public function delete(Tenant $tenant, DocumentFolder $documentFolder): bool
    {
        return $tenant->company_id === $documentFolder->company_id
            && $tenant->can('projects.documents.delete');
    }

    public function restore(Tenant $tenant, DocumentFolder $documentFolder): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, DocumentFolder $documentFolder): bool
    {
        return false;
    }
}
