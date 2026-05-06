<?php

namespace App\Policies\Hr;

use App\Models\Hr\LeaveRequest;
use App\Models\Tenant;

class LeaveRequestPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.leave.view');
    }

    public function view(Tenant $tenant, LeaveRequest $request): bool
    {
        return $tenant->company_id === $request->company_id
            && $tenant->can('hr.leave.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.leave.create');
    }

    public function update(Tenant $tenant, LeaveRequest $request): bool
    {
        return $tenant->company_id === $request->company_id
            && $tenant->can('hr.leave.edit');
    }

    public function delete(Tenant $tenant, LeaveRequest $request): bool
    {
        return $tenant->company_id === $request->company_id
            && $tenant->can('hr.leave.delete');
    }

    public function approve(Tenant $tenant, LeaveRequest $request): bool
    {
        return $tenant->company_id === $request->company_id
            && $tenant->can('hr.leave.approve');
    }

    public function restore(Tenant $tenant, LeaveRequest $request): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, LeaveRequest $request): bool
    {
        return false;
    }
}
