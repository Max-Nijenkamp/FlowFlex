<?php

declare(strict_types=1);

use App\Events\Foundation\CompanyCreated;
use App\Listeners\Foundation\SyncOwnerPermissionsListener;
use App\Models\Company;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('SyncOwnerPermissionsListener', function () {
    it('syncs all permissions to the company owner role on CompanyCreated', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $owner   = User::factory()->create(['company_id' => $company->id]);

        setPermissionsTeamId($company->id);
        $ownerRole = Role::create([
            'name'       => 'owner',
            'guard_name' => 'web',
            'team_id'    => $company->id,
        ]);

        expect($ownerRole->permissions)->toHaveCount(0);

        $listener = app(SyncOwnerPermissionsListener::class);
        $listener->handle(new CompanyCreated($company, $owner));

        expect($ownerRole->fresh()->permissions)->toHaveCount(Permission::count());
    });

    it('does nothing when no owner role exists for the company', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $owner   = User::factory()->create(['company_id' => $company->id]);

        // No role created — listener should not throw
        $listener = app(SyncOwnerPermissionsListener::class);
        expect(fn () => $listener->handle(new CompanyCreated($company, $owner)))->not->toThrow(\Throwable::class);
    });

    it('does not sync permissions to non-owner roles', function () {
        $company = Company::factory()->create(['status' => 'active']);
        $owner   = User::factory()->create(['company_id' => $company->id]);

        setPermissionsTeamId($company->id);
        Role::create(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id]);
        $memberRole = Role::create(['name' => 'member', 'guard_name' => 'web', 'team_id' => $company->id]);

        $listener = app(SyncOwnerPermissionsListener::class);
        $listener->handle(new CompanyCreated($company, $owner));

        expect($memberRole->fresh()->permissions)->toHaveCount(0);
    });
});
