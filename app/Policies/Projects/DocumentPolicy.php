<?php

namespace App\Policies\Projects;

use App\Models\Projects\Document;
use App\Models\Tenant;

class DocumentPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.documents.view');
    }

    public function view(Tenant $tenant, Document $document): bool
    {
        return $tenant->company_id === $document->company_id
            && $tenant->can('projects.documents.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.documents.create');
    }

    public function update(Tenant $tenant, Document $document): bool
    {
        return $tenant->company_id === $document->company_id
            && $tenant->can('projects.documents.edit');
    }

    public function delete(Tenant $tenant, Document $document): bool
    {
        return $tenant->company_id === $document->company_id
            && $tenant->can('projects.documents.delete');
    }

    public function restore(Tenant $tenant, Document $document): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Document $document): bool
    {
        return false;
    }
}
