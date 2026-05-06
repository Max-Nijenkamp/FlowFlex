<?php

namespace App\Policies\Hr;

use App\Models\Hr\LeaveType;
use App\Models\Tenant;

class LeaveTypePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.leave.view');
    }

    public function view(Tenant $tenant, LeaveType $leaveType): bool
    {
        return $tenant->company_id === $leaveType->company_id
            && $tenant->can('hr.leave.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.leave.create');
    }

    public function update(Tenant $tenant, LeaveType $leaveType): bool
    {
        return $tenant->company_id === $leaveType->company_id
            && $tenant->can('hr.leave.edit');
    }

    public function delete(Tenant $tenant, LeaveType $leaveType): bool
    {
        return $tenant->company_id === $leaveType->company_id
            && $tenant->can('hr.leave.delete');
    }

    public function restore(Tenant $tenant, LeaveType $leaveType): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, LeaveType $leaveType): bool
    {
        return false;
    }
}
