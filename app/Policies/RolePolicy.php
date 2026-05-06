<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Role $role): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Role $role): bool { return true; }
    public function delete(User $user, Role $role): bool {
        return ! in_array($role->name, ['super-admin', 'workspace-admin', 'hr-manager', 'employee']);
    }
    public function restore(User $user, Role $role): bool { return true; }
    public function forceDelete(User $user, Role $role): bool { return false; }
}
