<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use App\Support\Services\WorkspacePanels;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

function hubCompany(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');

    test()->actingAs($owner);
    Filament::setCurrentPanel('app');

    return [$company, $owner];
}

function activateForHub(Company $company, string ...$keys): void
{
    foreach ($keys as $key) {
        CompanyModuleSubscription::query()->firstOrCreate(
            ['company_id' => $company->id, 'module_key' => $key, 'deactivated_at' => null],
            ['activated_at' => now()],
        );
    }
    Cache::forget("company:{$company->id}:modules");
}

test('switcher rows are the intersection of active modules and access permissions, alphabetical', function () {
    [$company] = hubCompany();
    activateForHub($company, 'hr.leave', 'crm.deals'); // finance NOT active

    $tiles = WorkspacePanels::tiles();

    expect($tiles->pluck('key')->all())->toBe(['crm', 'hr']) // CRM < HR alphabetically by name
        ->and($tiles->pluck('key'))->not->toContain('finance');
});

test('an inactive domain yields no row even with the permission; no permission hides an active domain', function () {
    [$company] = hubCompany();
    activateForHub($company, 'hr.leave');

    $member = User::factory()->for($company)->create();
    $role = Role::query()->create(['name' => 'hub-only', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.hub.view'); // NO access.hr
    $member->assignRole($role);

    $this->actingAs($member);
    expect(WorkspacePanels::tiles())->toBeEmpty(); // active module, no access permission

    $role->givePermissionTo('access.finance'); // permission for an INACTIVE domain
    expect(WorkspacePanels::tiles())->toBeEmpty();
});

test('company A rows never leak into company B', function () {
    [$companyA] = hubCompany();
    activateForHub($companyA, 'hr.leave');

    hubCompany(); // switches context to company B

    expect(WorkspacePanels::tiles())->toBeEmpty();
});

test('the switcher lives in the sidebar and always lists the current workspace', function () {
    hubCompany();

    $this->get('/app')
        ->assertOk()
        ->assertSee('Switch workspace')
        ->assertSee('ff-ws-trigger', escape: false)
        ->assertSee('ff-current', escape: false);
});

test('the switcher is hidden without core.hub.view', function () {
    [$company] = hubCompany();

    $stranger = User::factory()->for($company)->create(); // no roles at all
    $this->flushSession();
    $this->actingAs($stranger);

    expect(WorkspacePanels::canView())->toBeFalse();

    $this->get('/app')
        ->assertOk()
        ->assertDontSee('ff-ws-trigger', escape: false);
});

test('the empty modal offers the marketplace to owners and directs members to their admin', function () {
    [$company] = hubCompany();

    $this->get('/app')
        ->assertSee('Open the marketplace');

    $member = User::factory()->for($company)->create();
    $role = Role::query()->create(['name' => 'plain', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.hub.view');
    $member->assignRole($role);

    // fresh session — AuthenticateSession rejects a user swap mid-session
    $this->flushSession();
    $this->actingAs($member);
    $this->get('/app')
        ->assertSee('Ask your workspace admin')
        ->assertDontSee('Open the marketplace');
});
