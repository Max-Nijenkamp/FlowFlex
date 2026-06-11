<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\LocalDevSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('seeds permissions idempotently', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(PermissionSeeder::class);

    expect(Permission::where('guard_name', 'web')->count())->toBe(count(PermissionSeeder::PERMISSIONS));
});

it('gives the demo owner role every permission', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(LocalDevSeeder::class);

    $company = Company::where('slug', 'flowflex-demo')->firstOrFail();
    setPermissionsTeamId($company->id);
    $owner = Role::where(['name' => 'owner', 'team_id' => $company->id])->firstOrFail();

    expect($owner->permissions()->count())->toBe(Permission::where('guard_name', 'web')->count());
});

it('grants newly seeded permissions to owner on re-seed', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(LocalDevSeeder::class);

    $company = Company::where('slug', 'flowflex-demo')->firstOrFail();
    setPermissionsTeamId($company->id);
    Permission::findOrCreate('company.audit.view-any', 'web');

    $this->seed(LocalDevSeeder::class); // re-sync

    $owner = Role::where(['name' => 'owner', 'team_id' => $company->id])->firstOrFail();
    expect($owner->hasPermissionTo('company.audit.view-any'))->toBeTrue();
});

it('creates working demo logins', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(LocalDevSeeder::class);

    expect(Admin::where('email', 'admin@flowflex.nl')->exists())->toBeTrue()
        ->and(User::where('email', 'demo@flowflex.nl')->withoutGlobalScopes()->exists())->toBeTrue();
});

it('refuses to run LocalDevSeeder in production', function () {
    app()->detectEnvironment(fn () => 'production');

    (new LocalDevSeeder)->run();
})->throws(RuntimeException::class);
