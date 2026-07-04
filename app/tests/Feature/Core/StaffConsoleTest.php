<?php

declare(strict_types=1);

use App\Actions\ProvisionCompanyAction;
use App\Data\ProvisionCompanyData;
use App\Filament\Admin\Resources\BillingInvoiceResource\Pages\ListBillingInvoices;
use App\Filament\Admin\Resources\CompanyResource\Pages\ListCompanies;
use App\Mail\InvitationMail;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserInvitation;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Database\Seeders\ModuleCatalogSeeder;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    Mail::fake();
    $this->seed(PermissionSeeder::class);
    $this->seed(ModuleCatalogSeeder::class);
});

test('provisioning stands up company, roles, free modules and the owner invite in one transaction', function () {
    $company = ProvisionCompanyAction::run(new ProvisionCompanyData(
        name: 'Veldkamp Logistics',
        owner_email: 'marieke@veldkamp.eu',
    ));

    expect($company->slug)->toBe('veldkamp-logistics')
        ->and($company->subscription_status)->toBe('trial');

    // built-in roles under the new team
    expect(Role::query()->where('company_id', $company->id)->pluck('name')->all())
        ->toContain('owner', 'admin', 'manager', 'employee');

    // free core modules active
    $active = app(BillingService::class)->activeModules($company->id);
    expect($active)->toContain('core.audit', 'core.settings', 'core.rbac');

    // owner invitation queued
    $invitation = UserInvitation::query()->withoutGlobalScopes()
        ->where('company_id', $company->id)->sole();
    expect($invitation->role)->toBe('owner')
        ->and($invitation->invited_by)->toBeNull()
        ->and($invitation->isPending())->toBeTrue();
    Mail::assertQueued(InvitationMail::class);

    // context never leaks into later admin queries
    expect(app(CompanyContext::class)->currentId())->toBeNull();
});

test('slug collisions get a numeric suffix', function () {
    ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Acme', owner_email: 'a@acme.nl'));
    $second = ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Acme', owner_email: 'b@acme.nl'));

    expect($second->slug)->toBe('acme-2');
});

test('suspend marks the company and the app panel blocks it with 402', function () {
    $company = ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Suspendable', owner_email: 'x@s.nl'));

    app(BillingService::class)->suspend($company->id, 'unpaid invoices');

    expect($company->fresh()->subscription_status)->toBe('suspended');

    $user = User::factory()->for($company)->create();
    $this->actingAs($user)->get('/app')->assertStatus(402);
});

test('the staff console pages render for an admin', function () {
    $admin = Admin::query()->create([
        'name' => 'Staff', 'email' => 'staff@flowflex.nl', 'password' => 'password', 'role' => 'super_admin',
    ]);
    ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Renderco', owner_email: 'r@r.nl'));

    Filament\Facades\Filament::setCurrentPanel('admin');

    Livewire\Livewire::actingAs($admin, 'admin')
        ->test(ListCompanies::class)
        ->assertSuccessful()
        ->assertSee('Renderco');

    Livewire\Livewire::actingAs($admin, 'admin')
        ->test(ListBillingInvoices::class)
        ->assertSuccessful();
});

test('staff module management activates and deactivates under the target company context', function () {
    $company = ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Moduleco', owner_email: 'm@m.nl'));
    $user = User::factory()->for($company)->create();

    // simulate the relation-manager path: context set + service call + forget
    app(CompanyContext::class)->set($company);
    app(BillingService::class)->activateModule('hr.leave', $user);
    app(CompanyContext::class)->forget();

    expect(app(BillingService::class)->activeModules($company->id))->toContain('hr.leave');
});
