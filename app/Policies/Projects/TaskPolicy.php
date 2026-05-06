<?php

namespace App\Policies\Projects;

use App\Models\Projects\Task;
use App\Models\Tenant;

class TaskPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.tasks.view');
    }

    public function view(Tenant $tenant, Task $task): bool
    {
        return $tenant->company_id === $task->company_id
            && $tenant->can('projects.tasks.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.tasks.create');
    }

    public function update(Tenant $tenant, Task $task): bool
    {
        return $tenant->company_id === $task->company_id
            && $tenant->can('projects.tasks.edit');
    }

    public function delete(Tenant $tenant, Task $task): bool
    {
        return $tenant->company_id === $task->company_id
            && $tenant->can('projects.tasks.delete');
    }

    public function restore(Tenant $tenant, Task $task): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Task $task): bool
    {
        return false;
    }
}
