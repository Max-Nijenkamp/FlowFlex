<?php

declare(strict_types=1);

use App\Filament\App\Pages\WorkspaceHubPage;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
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

test('tiles are the intersection of active modules and access permissions, alphabetical', function () {
    [$company] = hubCompany();
    activateForHub($company, 'hr.leave', 'crm.deals'); // finance NOT active

    $tiles = Livewire::test(WorkspaceHubPage::class)->instance()->tiles;

    expect($tiles->pluck('key')->all())->toBe(['crm', 'hr']) // CRM < HR alphabetically by name
        ->and($tiles->pluck('key'))->not->toContain('finance');
});

test('an inactive domain yields no tile even with the permission; no permission hides an active domain', function () {
    [$company] = hubCompany();
    activateForHub($company, 'hr.leave');

    $member = User::factory()->for($company)->create();
    $role = Role::query()->create(['name' => 'hub-only', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.hub.view'); // NO access.hr
    $member->assignRole($role);

    $this->actingAs($member);
    $tiles = Livewire::test(WorkspaceHubPage::class)->instance()->tiles;
    expect($tiles)->toBeEmpty(); // active module, no access permission

    $role->givePermissionTo('access.finance'); // permission for an INACTIVE domain
    $tiles = Livewire::test(WorkspaceHubPage::class)->instance()->tiles;
    expect($tiles)->toBeEmpty();
});

test('company A tiles never leak into company B', function () {
    [$companyA] = hubCompany();
    activateForHub($companyA, 'hr.leave');

    [$companyB] = hubCompany();

    $tiles = Livewire::test(WorkspaceHubPage::class)->instance()->tiles;
    expect($tiles)->toBeEmpty();
});

test('the empty state offers the marketplace to owners and directs members to their admin', function () {
    [$company] = hubCompany();

    Livewire::test(WorkspaceHubPage::class)
        ->assertSee('Nothing switched on yet')
        ->assertSee('marketplace');

    $member = User::factory()->for($company)->create();
    $role = Role::query()->create(['name' => 'plain', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.hub.view');
    $member->assignRole($role);

    $this->actingAs($member);
    Livewire::test(WorkspaceHubPage::class)
        ->assertSee('Ask your workspace admin')
        ->assertDontSee('Open the marketplace');
});

test('the hub is denied without core.hub.view', function () {
    [$company] = hubCompany();

    $stranger = User::factory()->for($company)->create(); // no roles at all
    $this->actingAs($stranger);

    expect(WorkspaceHubPage::canAccess())->toBeFalse();
});
