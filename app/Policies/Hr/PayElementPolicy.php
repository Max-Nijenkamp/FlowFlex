<?php

namespace App\Policies\Hr;

use App\Models\Hr\PayElement;
use App\Models\Tenant;

class PayElementPolicy
{
    public function viewAny(Tenant $tenant): bool
    {
        return $tenant->can('hr.pay-elements.view');
    }

    public function view(Tenant $tenant, PayElement $element): bool
    {
        return $tenant->company_id === $element->company_id
            && $tenant->can('hr.pay-elements.view');
    }

    public function create(Tenant $tenant): bool
    {
        return $tenant->can('hr.pay-elements.create');
    }

    public function update(Tenant $tenant, PayElement $element): bool
    {
        return $tenant->company_id === $element->company_id
            && $tenant->can('hr.pay-elements.edit');
    }

    public function delete(Tenant $tenant, PayElement $element): bool
    {
        return $tenant->company_id === $element->company_id
            && $tenant->can('hr.pay-elements.delete');
    }

    public function restore(Tenant $tenant, PayElement $element): bool
    {
        return false;
    }

    public function forceDelete(Tenant $tenant, PayElement $element): bool
    {
        return false;
    }
}
