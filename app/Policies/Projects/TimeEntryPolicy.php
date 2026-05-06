<?php

namespace App\Policies\Projects;

use App\Models\Projects\TimeEntry;
use App\Models\Tenant;

class TimeEntryPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.time.view');
    }

    public function view(Tenant $tenant, TimeEntry $timeEntry): bool
    {
        return $tenant->company_id === $timeEntry->company_id
            && $tenant->can('projects.time.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.time.create');
    }

    public function update(Tenant $tenant, TimeEntry $timeEntry): bool
    {
        return $tenant->company_id === $timeEntry->company_id
            && $tenant->can('projects.time.edit');
    }

    public function delete(Tenant $tenant, TimeEntry $timeEntry): bool
    {
        return $tenant->company_id === $timeEntry->company_id
            && $tenant->can('projects.time.delete');
    }

    public function approve(Tenant $tenant): bool
    {
        return $tenant->can('projects.time.approve');
    }

    public function restore(Tenant $tenant, TimeEntry $timeEntry): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, TimeEntry $timeEntry): bool
    {
        return false;
    }
}
