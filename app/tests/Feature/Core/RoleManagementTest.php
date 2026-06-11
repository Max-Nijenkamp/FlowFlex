<?php

declare(strict_types=1);

use App\Actions\Core\AssignRolesAction;
use App\Actions\Core\CreateRoleAction;
use App\Actions\Core\DeleteRoleAction;
use App\Data\Core\AssignRolesData;
use App\Data\Core\CreateRoleData;
use App\Exceptions\Core\CannotDeleteBuiltInRoleException;
use App\Exceptions\Core\CannotRemoveLastOwnerException;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PermissionSeeder::class);
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
});

it('creates a custom role with a permission subset', function () {
    $role = CreateRoleAction::run(new CreateRoleData('hr-viewer', ['core.rbac.view-any', 'core.settings.view']));

    expect($role->team_id)->toBe($this->company->id)
        ->and($role->permissions)->toHaveCount(2);
});

it('grants the union of permissions across multiple roles', function () {
    $user = User::factory()->forCompany($this->company)->create();
    $a = CreateRoleAction::run(new CreateRoleData('role-a', ['core.settings.view']));
    $b = CreateRoleAction::run(new CreateRoleData('role-b', ['core.audit.view-any']));
    $user->assignRole([$a, $b]);

    expect($user->can('core.settings.view'))->toBeTrue()
        ->and($user->can('core.audit.view-any'))->toBeTrue()
        ->and($user->can('core.rbac.delete'))->toBeFalse();
});

it('refuses to demote the last owner', function () {
    $owner = User::factory()->forCompany($this->company)->create();
    Role::create(['name' => 'owner', 'guard_name' => 'web']);
    Role::create(['name' => 'employee', 'guard_name' => 'web']);
    $owner->assignRole('owner');

    AssignRolesAction::run(new AssignRolesData(user_id: $owner->id, roles: ['employee']));
})->throws(CannotRemoveLastOwnerException::class);

it('allows demotion when another owner remains', function () {
    Role::create(['name' => 'owner', 'guard_name' => 'web']);
    Role::create(['name' => 'employee', 'guard_name' => 'web']);
    $first = User::factory()->forCompany($this->company)->create();
    $second = User::factory()->forCompany($this->company)->create();
    $first->assignRole('owner');
    $second->assignRole('owner');

    AssignRolesAction::run(new AssignRolesData(user_id: $first->id, roles: ['employee']));

    expect($first->fresh()->hasRole('employee'))->toBeTrue()
        ->and($first->fresh()->hasRole('owner'))->toBeFalse();
});

it('refuses to delete built-in roles', function () {
    $role = Role::create(['name' => 'owner', 'guard_name' => 'web']);

    DeleteRoleAction::run($role->id);
})->throws(CannotDeleteBuiltInRoleException::class);

it('refuses to delete a role with users still assigned', function () {
    $role = CreateRoleAction::run(new CreateRoleData('temp'));
    $user = User::factory()->forCompany($this->company)->create();
    $user->assignRole($role);

    DeleteRoleAction::run($role->id);
})->throws(RuntimeException::class);

it('keeps roles isolated between company teams', function () {
    CreateRoleAction::run(new CreateRoleData('only-in-a'));

    $other = Company::factory()->create();
    $this->setCompany($other);

    expect(Role::where('team_id', $other->id)->where('name', 'only-in-a')->exists())->toBeFalse();
});
