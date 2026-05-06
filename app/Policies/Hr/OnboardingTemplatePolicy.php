<?php

namespace App\Policies\Hr;

use App\Models\Hr\OnboardingTemplate;
use App\Models\Tenant;

class OnboardingTemplatePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.onboarding.view');
    }

    public function view(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return $tenant->company_id === $template->company_id
            && $tenant->can('hr.onboarding.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.onboarding.create');
    }

    public function update(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return $tenant->company_id === $template->company_id
            && $tenant->can('hr.onboarding.edit');
    }

    public function delete(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return $tenant->company_id === $template->company_id
            && $tenant->can('hr.onboarding.delete');
    }

    public function restore(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return false;
    }
}
