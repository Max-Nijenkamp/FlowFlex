<?php

namespace App\Policies\Projects;

use App\Models\Projects\TaskAutomation;
use App\Models\Tenant;

class TaskAutomationPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.tasks.view');
    }

    public function view(Tenant $tenant, TaskAutomation $taskAutomation): bool
    {
        return $tenant->company_id === $taskAutomation->company_id
            && $tenant->can('projects.tasks.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.tasks.create');
    }

    public function update(Tenant $tenant, TaskAutomation $taskAutomation): bool
    {
        return $tenant->company_id === $taskAutomation->company_id
            && $tenant->can('projects.tasks.edit');
    }

    public function delete(Tenant $tenant, TaskAutomation $taskAutomation): bool
    {
        return $tenant->company_id === $taskAutomation->company_id
            && $tenant->can('projects.tasks.delete');
    }

    public function restore(Tenant $tenant, TaskAutomation $taskAutomation): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, TaskAutomation $taskAutomation): bool
    {
        return false;
    }
}
