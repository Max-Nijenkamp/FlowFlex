<?php

declare(strict_types=1);

use App\Actions\AssignRolesAction;
use App\Actions\CreateRoleAction;
use App\Actions\DeleteRoleAction;
use App\Actions\TransferOwnershipAction;
use App\Data\AssignRolesData;
use App\Data\CreateRoleData;
use App\Exceptions\CannotDeleteBuiltInRoleException;
use App\Exceptions\CannotRemoveLastOwnerException;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

function rbacCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    foreach (['core.rbac', 'core.audit', 'core.settings'] as $key) {
        CompanyModuleSubscription::query()->firstOrCreate(
            ['company_id' => $company->id, 'module_key' => $key, 'deactivated_at' => null],
            ['activated_at' => now()],
        );
    }
    Cache::forget("company:{$company->id}:modules");

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');

    return [$company, $owner];
}

test('built-in roles exist with the owner holding everything and admin everything but transfer', function () {
    [, $owner] = rbacCompany();

    expect($owner->can('core.rbac.transfer-ownership'))->toBeTrue();

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    expect($admin->can('core.rbac.view-any'))->toBeTrue()
        ->and($admin->can('core.rbac.transfer-ownership'))->toBeFalse();
});

test('a custom role can only hold permissions of active modules', function () {
    rbacCompany();

    $role = CreateRoleAction::run(new CreateRoleData('auditor', ['core.audit.view-any']));
    expect($role->hasPermissionTo('core.audit.view-any'))->toBeTrue();

    // hr.leave is in the catalog but NOT active for this company
    expect(fn () => CreateRoleAction::run(new CreateRoleData('hr-role', ['hr.leave.view-any'])))
        ->toThrow(InvalidArgumentException::class);
});

test('demoting the sole owner throws and aborts the assignment', function () {
    [, $owner] = rbacCompany();

    expect(fn () => AssignRolesAction::run(new AssignRolesData($owner->id, ['admin'])))
        ->toThrow(CannotRemoveLastOwnerException::class);

    expect($owner->fresh()->hasRole('owner'))->toBeTrue();
});

test('the owner role cannot be handed out via assignment', function () {
    rbacCompany();

    $member = User::factory()->create();
    $member->assignRole('employee');

    expect(fn () => AssignRolesAction::run(new AssignRolesData($member->id, ['owner'])))
        ->toThrow(InvalidArgumentException::class);
});

test('deleting a built-in role throws; a custom role with members is refused', function () {
    rbacCompany();

    $builtIn = Role::query()->where('name', 'admin')->firstOrFail();
    expect(fn () => DeleteRoleAction::run((string) $builtIn->id))
        ->toThrow(CannotDeleteBuiltInRoleException::class);

    $custom = CreateRoleAction::run(new CreateRoleData('temp'));
    $member = User::factory()->create();
    $member->assignRole('temp');

    expect(fn () => DeleteRoleAction::run((string) $custom->id))
        ->toThrow(InvalidArgumentException::class);

    $member->removeRole('temp');
    DeleteRoleAction::run((string) $custom->id);
    expect(Role::query()->where('name', 'temp')->exists())->toBeFalse();
});

test('ownership transfer is atomic: exactly one owner before and after', function () {
    [, $owner] = rbacCompany();

    $successor = User::factory()->create(['email_verified_at' => now()]);
    $successor->assignRole('employee');

    TransferOwnershipAction::run($successor->id);

    expect($successor->fresh()->hasRole('owner'))->toBeTrue()
        ->and($owner->fresh()->hasRole('owner'))->toBeFalse()
        ->and($owner->fresh()->hasRole('admin'))->toBeTrue();

    $ownerCount = User::query()->get()->filter(fn (User $u): bool => $u->hasRole('owner'))->count();
    expect($ownerCount)->toBe(1);
});

test('transfer to an unverified member is rejected', function () {
    rbacCompany();

    $unverified = User::factory()->create(['email_verified_at' => null]);

    expect(fn () => TransferOwnershipAction::run($unverified->id))
        ->toThrow(InvalidArgumentException::class);
});
