<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Company;

class CompanyPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Company $company): bool { return true; }
    public function create(User $user): bool { return true; }
    public function update(User $user, Company $company): bool { return true; }
    public function delete(User $user, Company $company): bool { return true; }
    public function restore(User $user, Company $company): bool { return true; }
    public function forceDelete(User $user, Company $company): bool { return false; }
}
