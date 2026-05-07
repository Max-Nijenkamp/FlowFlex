<?php

namespace App\Policies\Hr;

use App\Models\Hr\OnboardingFlow;
use App\Models\Tenant;

class OnboardingFlowPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.onboarding-flows.view');
    }

    public function view(Tenant $tenant, OnboardingFlow $flow): bool
    {
        return $tenant->company_id === $flow->company_id
            && $tenant->can('hr.onboarding-flows.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.onboarding-flows.create');
    }

    public function update(Tenant $tenant, OnboardingFlow $flow): bool
    {
        return $tenant->company_id === $flow->company_id
            && $tenant->can('hr.onboarding-flows.edit');
    }

    public function delete(Tenant $tenant, OnboardingFlow $flow): bool
    {
        return $tenant->company_id === $flow->company_id
            && $tenant->can('hr.onboarding-flows.delete');
    }

    public function restore(Tenant $tenant, OnboardingFlow $flow): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, OnboardingFlow $flow): bool
    {
        return false;
    }
}
