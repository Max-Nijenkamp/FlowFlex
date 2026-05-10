<?php

declare(strict_types=1);

use App\Filament\App\Pages\ModuleMarketplace;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

describe('Module Marketplace', function () {
    beforeEach(function () {
        auth()->guard('web')->logout();
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create(['company_id' => $this->company->id, 'status' => 'active']);

        setPermissionsTeamId($this->company->id);
        $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $permission = Permission::firstOrCreate([
            'name'       => 'core.modules.manage',
            'guard_name' => 'web',
        ]);
        $ownerRole->givePermissionTo($permission);
        $this->user->assignRole($ownerRole);

        app(CompanyContext::class)->set($this->company);
        $this->actingAs($this->user, 'web');

        // Seed a test module
        $this->module = ModuleCatalog::create([
            'module_key'            => 'test.feature-x',
            'domain'                => 'test',
            'name'                  => 'Feature X',
            'per_user_monthly_price' => 5.00,
            'is_active'             => true,
        ]);
    });

    it('renders the module marketplace page', function () {
        Livewire::test(ModuleMarketplace::class)
            ->assertSee('Feature X')
            ->assertSee('€5.00');
    });

    it('can enable a module', function () {
        Livewire::test(ModuleMarketplace::class)
            ->call('enableModule', 'test.feature-x')
            ->assertHasNoErrors();

        expect(
            CompanyModuleSubscription::withoutGlobalScopes()
                ->where('company_id', $this->company->id)
                ->where('module_key', 'test.feature-x')
                ->where('status', 'active')
                ->exists()
        )->toBeTrue();
    });

    it('can disable a module', function () {
        CompanyModuleSubscription::withoutGlobalScopes()->create([
            'company_id'   => $this->company->id,
            'module_key'   => 'test.feature-x',
            'status'       => 'active',
            'activated_at' => now(),
        ]);

        Livewire::test(ModuleMarketplace::class)
            ->call('disableModule', 'test.feature-x')
            ->assertHasNoErrors();

        expect(
            CompanyModuleSubscription::withoutGlobalScopes()
                ->where('company_id', $this->company->id)
                ->where('module_key', 'test.feature-x')
                ->where('status', 'inactive')
                ->exists()
        )->toBeTrue();
    });

    it('does not affect another company when enabling a module', function () {
        $otherCompany = Company::factory()->create(['status' => 'active']);

        Livewire::test(ModuleMarketplace::class)
            ->call('enableModule', 'test.feature-x');

        expect(
            CompanyModuleSubscription::withoutGlobalScopes()
                ->where('company_id', $otherCompany->id)
                ->where('module_key', 'test.feature-x')
                ->exists()
        )->toBeFalse();
    });

    it('shows a notification when enabling an inactive module key', function () {
        Livewire::test(ModuleMarketplace::class)
            ->call('enableModule', 'nonexistent.module')
            ->assertNotified();
    });

    it('non-owner cannot enable a module', function () {
        $nonOwner = User::factory()->create(['company_id' => $this->company->id, 'status' => 'active']);
        $this->actingAs($nonOwner, 'web');

        // abort_unless(403) is caught by Livewire internally — verify by asserting module was NOT activated
        try {
            Livewire::test(ModuleMarketplace::class)
                ->call('enableModule', 'test.feature-x');
        } catch (\Throwable) {
            // Expected: Livewire may propagate or swallow the 403
        }

        expect(
            CompanyModuleSubscription::withoutGlobalScopes()
                ->where('company_id', $this->company->id)
                ->where('module_key', 'test.feature-x')
                ->where('status', 'active')
                ->exists()
        )->toBeFalse();
    });
});
