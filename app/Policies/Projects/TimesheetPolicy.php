<?php

namespace App\Policies\Projects;

use App\Models\Projects\Timesheet;
use App\Models\Tenant;

class TimesheetPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('projects.time.view');
    }

    public function view(Tenant $tenant, Timesheet $timesheet): bool
    {
        return $tenant->company_id === $timesheet->company_id
            && $tenant->can('projects.time.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('projects.time.create');
    }

    public function update(Tenant $tenant, Timesheet $timesheet): bool
    {
        return $tenant->company_id === $timesheet->company_id
            && $tenant->can('projects.time.edit');
    }

    public function delete(Tenant $tenant, Timesheet $timesheet): bool
    {
        return $tenant->company_id === $timesheet->company_id
            && $tenant->can('projects.time.delete');
    }

    public function restore(Tenant $tenant, Timesheet $timesheet): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, Timesheet $timesheet): bool
    {
        return false;
    }
}
