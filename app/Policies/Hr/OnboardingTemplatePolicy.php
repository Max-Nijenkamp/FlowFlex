<?php

namespace App\Policies\Hr;

use App\Models\Hr\OnboardingTemplate;
use App\Models\Tenant;

class OnboardingTemplatePolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.onboarding-templates.view');
    }

    public function view(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return $tenant->company_id === $template->company_id
            && $tenant->can('hr.onboarding-templates.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.onboarding-templates.create');
    }

    public function update(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return $tenant->company_id === $template->company_id
            && $tenant->can('hr.onboarding-templates.edit');
    }

    public function delete(Tenant $tenant, OnboardingTemplate $template): bool
    {
        return $tenant->company_id === $template->company_id
            && $tenant->can('hr.onboarding-templates.delete');
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
