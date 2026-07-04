<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Role;

test('role names repeat across companies and assignments never leak', function () {
    $this->seed(PermissionSeeder::class);

    $companyA = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($companyA);
    $userA = User::factory()->for($companyA)->create();
    $userA->assignRole('admin');

    $companyB = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($companyB);
    $userB = User::factory()->for($companyB)->create();

    // Same role name exists per company, as separate team-scoped rows
    expect(Role::query()->where('name', 'admin')->count())->toBe(2);

    // Company B context: A's assignment is invisible/unusable
    expect($userB->hasRole('admin'))->toBeFalse()
        ->and($userB->can('core.rbac.view-any'))->toBeFalse();

    // Back in A's context the assignment still holds
    setCompany($companyA);
    expect($userA->fresh()->hasRole('admin'))->toBeTrue();
});
