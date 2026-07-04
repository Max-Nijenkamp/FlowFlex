<?php

declare(strict_types=1);

use App\Filament\App\Pages\CompanySettingsPage;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\User;
use App\Settings\CompanyLocaleSettings;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

function makeSettingsOwner(Company $company): User
{
    test()->seed(PermissionSeeder::class);

    $user = User::factory()->for($company)->create();
    $role = Role::query()->firstOrCreate(['name' => 'owner', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.settings.view', 'core.settings.manage');
    $user->assignRole($role);

    CompanyModuleSubscription::query()->firstOrCreate(
        ['company_id' => $company->id, 'module_key' => 'core.settings', 'deactivated_at' => null],
        ['activated_at' => now()],
    );
    Cache::forget("company:{$company->id}:modules");

    return $user;
}

test('settings load config defaults when a company has saved nothing', function () {
    setCompany(Company::factory()->create());

    $settings = app(CompanyLocaleSettings::class);

    expect($settings->timezone)->toBe('UTC')
        ->and($settings->locale)->toBe('en')
        ->and($settings->currency)->toBe('EUR');
});

test('a saved setting persists and is immediately readable (no stale cache)', function () {
    setCompany(Company::factory()->create());

    $settings = app(CompanyLocaleSettings::class);
    $settings->locale = 'nl';
    $settings->save();

    expect(app(CompanyLocaleSettings::class)->refresh()->locale)->toBe('nl');
});

test('company A settings never leak into company B', function () {
    setCompany(Company::factory()->create());
    $settings = app(CompanyLocaleSettings::class);
    $settings->locale = 'nl';
    $settings->timezone = 'Europe/Amsterdam';
    $settings->save();

    setCompany(Company::factory()->create());

    $other = app(CompanyLocaleSettings::class)->refresh();
    expect($other->locale)->toBe('en')
        ->and($other->timezone)->toBe('UTC');
});

test('the page is owner-only even with the right permissions', function () {
    $company = setCompany(Company::factory()->create());
    $owner = makeSettingsOwner($company);

    $nonOwner = User::factory()->for($company)->create();
    $editor = Role::query()->create(['name' => 'editor', 'guard_name' => 'web', 'company_id' => $company->id]);
    $editor->givePermissionTo('core.settings.view', 'core.settings.manage');
    $nonOwner->assignRole($editor);

    $this->actingAs($owner);
    expect(CompanySettingsPage::canAccess())->toBeTrue();

    $this->actingAs($nonOwner);
    expect(CompanySettingsPage::canAccess())->toBeFalse();
});

test('saving the locale tab mirrors locale to the company row and SetLocale picks it up', function () {
    $company = setCompany(Company::factory()->create());
    $owner = makeSettingsOwner($company);

    $this->actingAs($owner);
    Filament::setCurrentPanel('app');

    Livewire::test(CompanySettingsPage::class)
        ->fillForm([
            'timezone' => 'Europe/Amsterdam',
            'locale' => 'nl',
            'date_format' => 'd-m-Y',
            'currency' => 'EUR',
            'currency_position' => 'before',
            'decimal_places' => 2,
        ])
        ->call('saveLocale')
        ->assertNotified();

    expect($company->fresh()->locale)->toBe('nl');

    $this->actingAs($owner)->get('/app')->assertOk();
    expect(app()->getLocale())->toBe('nl');
});

test('the identity tab rejects a slug already taken by another company', function () {
    Company::factory()->create(['slug' => 'taken-slug']);
    $company = setCompany(Company::factory()->create());
    $owner = makeSettingsOwner($company);

    $this->actingAs($owner);
    Filament::setCurrentPanel('app');

    Livewire::test(CompanySettingsPage::class)
        ->fillForm([
            'name' => 'Acme',
            'slug' => 'taken-slug',
            'primary_color' => '#4F46E5',
        ])
        ->call('saveIdentity')
        ->assertHasFormErrors(['slug']);

    expect($company->fresh()->slug)->not->toBe('taken-slug');
});

test('saving identity mirrors name and slug onto the company row', function () {
    $company = setCompany(Company::factory()->create());
    $owner = makeSettingsOwner($company);

    $this->actingAs($owner);
    Filament::setCurrentPanel('app');

    Livewire::test(CompanySettingsPage::class)
        ->fillForm([
            'name' => 'Rebranded BV',
            'slug' => 'rebranded',
            'primary_color' => '#4F46E5',
        ])
        ->call('saveIdentity')
        ->assertNotified();

    $fresh = $company->fresh();
    expect($fresh->name)->toBe('Rebranded BV')
        ->and($fresh->slug)->toBe('rebranded');
});
