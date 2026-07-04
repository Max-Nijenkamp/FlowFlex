<?php

declare(strict_types=1);

use App\Contracts\BillingServiceInterface;
use App\Filament\App\Pages\ModuleMarketplacePage;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Support\Services\BuiltInRoles;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

function marketplaceOwner(): array
{
    test()->seed(PermissionSeeder::class);
    test()->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    $owner = User::factory()->for($company)->create();
    $owner->assignRole('owner');

    foreach (['core.marketplace', 'core.billing', 'core.audit'] as $key) {
        CompanyModuleSubscription::query()->firstOrCreate(
            ['company_id' => $company->id, 'module_key' => $key, 'deactivated_at' => null],
            ['activated_at' => now()],
        );
    }
    Cache::forget("company:{$company->id}:modules");

    test()->actingAs($owner);
    Filament::setCurrentPanel('app');

    return [$company, $owner];
}

test('the grid lists active catalog modules grouped by domain with a live price preview', function () {
    [$company] = marketplaceOwner();
    User::factory()->count(2)->for($company)->create(); // 3 users total

    $component = Livewire::test(ModuleMarketplacePage::class);
    $modules = $component->instance()->modules;

    expect($modules->keys()->all())->toContain('core', 'hr', 'finance', 'crm');

    $leave = $modules['hr']->firstWhere('module_key', 'hr.leave');
    // 300 cents x 3 users = EUR 9,00 per month
    expect($leave->price_preview)->toContain('9,00')
        ->and($leave->is_free)->toBeFalse()
        ->and($leave->is_subscribed)->toBeFalse();

    $audit = $modules['core']->firstWhere('module_key', 'core.audit');
    expect($audit->price_preview)->toBe('Included');
});

test('search narrows cards and unknown terms show the empty state', function () {
    marketplaceOwner();

    $component = Livewire::test(ModuleMarketplacePage::class)->set('search', 'leave');
    expect($component->instance()->modules->flatten(1))->toHaveCount(1);

    $component->set('search', 'zzz-not-a-module');
    expect($component->instance()->modules)->toBeEmpty();
    $component->assertSee('No modules match');
});

test('activating through the page flips hasModule and the card state', function () {
    [$company] = marketplaceOwner();

    Livewire::test(ModuleMarketplacePage::class)
        ->callAction('activate', arguments: ['key' => 'hr.leave', 'name' => 'Leave & absence'])
        ->assertNotified();

    expect(app(BillingServiceInterface::class)->hasModule('hr.leave'))->toBeTrue();

    $modules = Livewire::test(ModuleMarketplacePage::class)->instance()->modules;
    expect($modules['hr']->firstWhere('module_key', 'hr.leave')->is_subscribed)->toBeTrue();
});

test('deactivation keeps data but gates access; free-core modules refuse', function () {
    [$company, $owner] = marketplaceOwner();

    app(BillingServiceInterface::class)->activateModule('hr.leave', $owner);

    Livewire::test(ModuleMarketplacePage::class)
        ->callAction('deactivate', arguments: ['key' => 'hr.leave'])
        ->assertNotified();

    expect(app(BillingServiceInterface::class)->hasModule('hr.leave'))->toBeFalse()
        ->and(CompanyModuleSubscription::query()->where('module_key', 'hr.leave')->count())->toBe(1); // history retained

    // free-core module: the service refuses (surfaced as a danger notification)
    Livewire::test(ModuleMarketplacePage::class)
        ->callAction('deactivate', arguments: ['key' => 'core.audit'])
        ->assertNotified();
    expect(app(BillingServiceInterface::class)->hasModule('core.audit'))->toBeTrue();
});

test('company A activation never leaks into company B', function () {
    [, $ownerA] = marketplaceOwner();
    app(BillingServiceInterface::class)->activateModule('hr.leave', $ownerA);

    // fresh company B
    [$companyB] = marketplaceOwner();

    expect(app(BillingServiceInterface::class)->hasModule('hr.leave'))->toBeFalse();

    $modules = Livewire::test(ModuleMarketplacePage::class)->instance()->modules;
    expect($modules['hr']->firstWhere('module_key', 'hr.leave')->is_subscribed)->toBeFalse();
});

test('the page is denied without the permission or module gate', function () {
    [$company] = marketplaceOwner();

    $member = User::factory()->for($company)->create();
    $this->actingAs($member); // no role, no permissions

    expect(ModuleMarketplacePage::canAccess())->toBeFalse();
});
