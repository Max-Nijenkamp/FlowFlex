<?php

declare(strict_types=1);

use App\Exceptions\MissingCompanyContextException;
use App\Models\Company;
use App\Models\User;
use App\Support\Scopes\CompanyScope;
use App\Support\Services\CompanyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

it('only returns rows for the current company (CompanyScope)', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();
    User::factory()->forCompany($a)->count(2)->create();
    User::factory()->forCompany($b)->count(3)->create();

    $this->setCompany($a);
    expect(User::count())->toBe(2);

    $this->setCompany($b);
    expect(User::count())->toBe(3);
});

it('auto-fills company_id from context on create', function () {
    $company = Company::factory()->create();
    $this->setCompany($company);

    $user = User::create([
        'first_name' => 'Auto',
        'last_name' => 'Filled',
        'email' => 'auto@example.com',
        'password' => 'secret-secret',
    ]);

    expect($user->company_id)->toBe($company->id);
});

it('throws MissingCompanyContextException when context is required but absent', function () {
    app(CompanyContext::class)->forget();

    app(CompanyContext::class)->current();
})->throws(MissingCompanyContextException::class);

it('can bypass the scope with withoutGlobalScope (admin path)', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();
    User::factory()->forCompany($a)->create();
    User::factory()->forCompany($b)->create();

    $this->setCompany($a);

    expect(User::withoutGlobalScope(CompanyScope::class)->count())->toBe(2);
});

it('scopes roles per company team (same name coexists across teams)', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();

    setPermissionsTeamId($a->id);
    $roleA = Role::create(['name' => 'owner', 'guard_name' => 'web']);

    setPermissionsTeamId($b->id);
    $roleB = Role::create(['name' => 'owner', 'guard_name' => 'web']);

    // team_id auto-filled from the active team; identical name allowed under different teams
    expect($roleA->team_id)->toBe($a->id)
        ->and($roleB->team_id)->toBe($b->id)
        ->and(Role::count())->toBe(2);
});

it('resolves a user role only under its own company team', function () {
    $a = Company::factory()->create();
    $b = Company::factory()->create();
    $userA = User::factory()->forCompany($a)->create();

    setPermissionsTeamId($a->id);
    $roleA = Role::create(['name' => 'owner', 'guard_name' => 'web']);
    $userA->assignRole($roleA);

    setPermissionsTeamId($a->id);
    expect($userA->fresh()->hasRole('owner'))->toBeTrue();

    setPermissionsTeamId($b->id);
    expect($userA->fresh()->hasRole('owner'))->toBeFalse();
});
