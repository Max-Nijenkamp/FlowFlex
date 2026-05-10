<?php

declare(strict_types=1);

use App\Models\Company;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('Permission Seeder', function () {
    it('creates all 47 permissions', function () {
        // Pest.php already seeds permissions in beforeEach — just verify count
        expect(Permission::count())->toBe(47);
    });

    it('creates permissions idempotently', function () {
        $this->seed(PermissionSeeder::class);
        $this->seed(PermissionSeeder::class);

        expect(Permission::count())->toBe(47);
    });

    it('all permission names follow domain.module.action pattern', function () {
        Permission::all()->each(function (Permission $perm) {
            expect($perm->name)->toMatch('/^[a-z0-9-]+(?:\.[a-z0-9-]+){2,}$/');
        });
    });

    it('syncs permissions to every owner role', function () {
        $company = Company::factory()->create(['status' => 'active']);
        setPermissionsTeamId($company->id);

        $ownerRole = Role::create([
            'name'       => 'owner',
            'guard_name' => 'web',
            'team_id'    => $company->id,
        ]);

        $this->seed(PermissionSeeder::class);

        expect($ownerRole->fresh()->permissions)->toHaveCount(47);
    });

    it('syncs permissions to multiple owner roles across companies', function () {
        $companyA = Company::factory()->create(['status' => 'active']);
        $companyB = Company::factory()->create(['status' => 'active']);

        setPermissionsTeamId($companyA->id);
        $roleA = Role::create(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $companyA->id]);

        setPermissionsTeamId($companyB->id);
        $roleB = Role::create(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $companyB->id]);

        $this->seed(PermissionSeeder::class);

        expect($roleA->fresh()->permissions)->toHaveCount(47);
        expect($roleB->fresh()->permissions)->toHaveCount(47);
    });

    it('does not grant permissions to non-owner roles', function () {
        $company = Company::factory()->create(['status' => 'active']);
        setPermissionsTeamId($company->id);

        $memberRole = Role::create([
            'name'       => 'member',
            'guard_name' => 'web',
            'team_id'    => $company->id,
        ]);

        $this->seed(PermissionSeeder::class);

        expect($memberRole->fresh()->permissions)->toHaveCount(0);
    });
});
