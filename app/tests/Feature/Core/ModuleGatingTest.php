<?php

declare(strict_types=1);

use App\Filament\App\Resources\AuditLogResource;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Services\BillingService;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

test('hasModule is true only for an active subscription of the current company', function () {
    $company = setCompany(Company::factory()->create());

    expect(app(BillingService::class)->hasModule('core.audit'))->toBeFalse();

    CompanyModuleSubscription::query()->create([
        'company_id' => $company->id,
        'module_key' => 'core.audit',
        'activated_at' => now(),
    ]);
    Cache::forget("company:{$company->id}:modules");

    expect(app(BillingService::class)->hasModule('core.audit'))->toBeTrue();

    // Another company sees nothing
    $other = setCompany(Company::factory()->create());
    expect(app(BillingService::class)->hasModule('core.audit'))->toBeFalse();
});

test('hasModule fails closed without tenant context', function () {
    expect(app(BillingService::class)->hasModule('core.audit'))->toBeFalse();
});

test('activation is idempotent and deactivation preserves history', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    ModuleCatalogEntry::factory()->create(['module_key' => 'hr.leave', 'per_user_monthly_price' => 300]);

    $billing = app(BillingService::class);
    $first = $billing->activateModule('hr.leave', $user);
    $second = $billing->activateModule('hr.leave', $user);

    expect($second->id)->toBe($first->id)
        ->and($billing->hasModule('hr.leave'))->toBeTrue();

    $billing->deactivateModule('hr.leave');

    expect($billing->hasModule('hr.leave'))->toBeFalse()
        ->and(CompanyModuleSubscription::query()->withTrashed()->count())->toBe(1)
        ->and(CompanyModuleSubscription::query()->sole()->deactivated_at)->not->toBeNull();
});

test('a free core module cannot be deactivated and unknown modules cannot be activated', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    ModuleCatalogEntry::factory()->free()->create(['module_key' => 'core.audit']);

    expect(fn () => app(BillingService::class)->deactivateModule('core.audit'))
        ->toThrow(InvalidArgumentException::class);

    expect(fn () => app(BillingService::class)->activateModule('nope.nothing', $user))
        ->toThrow(InvalidArgumentException::class);
});

test('AuditLogResource is gated on permission AND module subscription', function () {
    $this->seed(PermissionSeeder::class);
    $this->seed(ModuleCatalogSeeder::class);

    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();
    $role = Role::query()->create(['name' => 'auditor', 'guard_name' => 'web', 'company_id' => $company->id]);
    $role->givePermissionTo('core.audit.view-any');

    $this->actingAs($user);

    // no permission, no module
    expect(AuditLogResource::canAccess())->toBeFalse();

    // permission only
    $user->assignRole($role);
    expect(AuditLogResource::canAccess())->toBeFalse();

    // permission + active module
    CompanyModuleSubscription::query()->create([
        'company_id' => $company->id,
        'module_key' => 'core.audit',
        'activated_at' => now(),
    ]);
    Cache::forget("company:{$company->id}:modules");

    expect(AuditLogResource::canAccess())->toBeTrue();

    // read-only surface
    expect(AuditLogResource::canCreate())->toBeFalse();
});

test('the module catalog seeder is idempotent and prices free core modules at zero', function () {
    $this->seed(ModuleCatalogSeeder::class);
    $this->seed(ModuleCatalogSeeder::class);

    expect(ModuleCatalogEntry::query()->count())->toBe(count(ModuleCatalogSeeder::CATALOG))
        ->and(ModuleCatalogEntry::query()->where('module_key', 'core.audit')->sole()->isFree())->toBeTrue();
});
