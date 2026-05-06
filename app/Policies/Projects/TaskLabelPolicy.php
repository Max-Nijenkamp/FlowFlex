<?php

namespace App\Policies\Projects;

use App\Models\Projects\TaskLabel;
use App\Models\Tenant;

class TaskLabelPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.tasks.view');
    }

    public function view(Tenant $tenant, TaskLabel $taskLabel): bool
    {
        return $tenant->company_id === $taskLabel->company_id
            && $tenant->can('projects.tasks.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.tasks.create');
    }

    public function update(Tenant $tenant, TaskLabel $taskLabel): bool
    {
        return $tenant->company_id === $taskLabel->company_id
            && $tenant->can('projects.tasks.edit');
    }

    public function delete(Tenant $tenant, TaskLabel $taskLabel): bool
    {
        return $tenant->company_id === $taskLabel->company_id
            && $tenant->can('projects.tasks.delete');
    }

    public function restore(Tenant $tenant, TaskLabel $taskLabel): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, TaskLabel $taskLabel): bool
    {
        return false;
    }
}
