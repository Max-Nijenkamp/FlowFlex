<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tenant;

class TenantPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Tenant $tenant): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Tenant $tenant): bool { return true; }
    public function delete(User $user, Tenant $tenant): bool { return true; }
    public function restore(User $user, Tenant $tenant): bool { return true; }
    public function forceDelete(User $user, Tenant $tenant): bool { return false; }
}
