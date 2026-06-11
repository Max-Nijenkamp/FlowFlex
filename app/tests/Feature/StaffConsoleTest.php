<?php

declare(strict_types=1);

use App\Actions\ProvisionCompanyAction;
use App\Contracts\BillingServiceInterface;
use App\Data\ProvisionCompanyData;
use App\Filament\Admin\Widgets\PlatformStatsWidget;
use App\Http\Middleware\SetCompanyContext;
use App\Mail\InvitationMail;
use App\Models\Admin;
use App\Models\BillingInvoice;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Services\CompanyContext;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Livewire\Mechanisms\PersistentMiddleware\PersistentMiddleware;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(fn () => app(CompanyContext::class)->forget());

it('rejects tenant users from the staff console', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $this->actingAs($user, 'web')->get('/admin/companies')->assertRedirect();
});

it('shows the companies list to staff admins', function () {
    Company::factory()->count(2)->create();

    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get('/admin/companies')
        ->assertSuccessful();
});

it('serves the staff console resources', function (string $path) {
    $this->actingAs(Admin::factory()->create(), 'admin')
        ->get($path)
        ->assertSuccessful();
})->with(['/admin/billing-invoices', '/admin/admins', '/admin/users', '/admin/activities']);

// Regression: deferred tables + actions run through Livewire update POSTs,
// which only re-run PERSISTENT middleware. Without SetCompanyContext there,
// every tenant permission/module check fails → 403 modals on all /app pages.
it('keeps tenant context middleware persistent for Livewire requests', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    // Booting the panel registers its persistent middleware with Livewire.
    $this->actingAs($user, 'web')->get('/app')->assertSuccessful();

    $persistent = app(PersistentMiddleware::class)
        ->getPersistentMiddleware();

    expect($persistent)->toContain(SetCompanyContext::class);
});

// Founder decision 2026-06-11: settings + marketplace are owner-only — a
// permission grant alone must not open them.
it('restricts company settings and marketplace to owners', function (string $path) {
    $this->seed(PermissionSeeder::class);

    $company = Company::factory()->create();
    app(BillingServiceInterface::class)->seedFreeCoreModules($company->id);
    setPermissionsTeamId($company->id);

    $member = User::factory()->forCompany($company)->create();
    $managerRole = Role::create(['name' => 'manager', 'guard_name' => 'web', 'team_id' => $company->id]);
    $managerRole->givePermissionTo(['core.settings.update', 'core.marketplace.view']);
    $member->assignRole($managerRole);

    $owner = User::factory()->forCompany($company)->create();
    $ownerRole = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id]);
    $ownerRole->syncPermissions(Permission::where('guard_name', 'web')->get());
    $owner->assignRole($ownerRole);

    $this->actingAs($member, 'web')->get($path)->assertForbidden();

    auth('web')->logout();

    $this->actingAs($owner, 'web')->get($path)->assertSuccessful();
})->with(['/app/company-settings-page', '/app/module-marketplace-page']);

it('provisions a company with owner role, free core modules and owner invite', function () {
    Mail::fake();
    $this->seed(PermissionSeeder::class);

    $company = ProvisionCompanyAction::run(new ProvisionCompanyData(
        name: 'Acme Works',
        owner_email: 'owner@acme.test',
    ));

    expect($company->slug)->toBe('acme-works')
        ->and($company->subscription_status)->toBe('trialing')
        ->and($company->trial_ends_at)->not->toBeNull();

    // Free core modules seeded.
    $active = CompanyModuleSubscription::withoutGlobalScopes()
        ->where('company_id', $company->id)
        ->whereNull('deactivated_at')
        ->pluck('module_key');
    expect($active->all())->toEqualCanonicalizing(ModuleCatalog::freeCoreModules());

    // Owner role carries every tenant permission.
    setPermissionsTeamId($company->id);
    $role = Role::where('team_id', $company->id)->where('name', 'owner')->firstOrFail();
    expect($role->permissions()->count())
        ->toBe(Permission::where('guard_name', 'web')->count());

    // Owner invitation: staff-sent, no tenant sender.
    $invitation = UserInvitation::withoutGlobalScopes()
        ->where('company_id', $company->id)
        ->firstOrFail();
    expect($invitation->email)->toBe('owner@acme.test')
        ->and($invitation->role)->toBe('owner')
        ->and($invitation->invited_by)->toBeNull();

    Mail::assertQueued(InvitationMail::class, fn (InvitationMail $mail) => $mail->hasTo('owner@acme.test'));

    // Context never leaks into the admin request.
    expect(app(CompanyContext::class)->currentId())->toBeNull();
});

it('generates unique slugs for duplicate company names', function () {
    Mail::fake();
    $this->seed(PermissionSeeder::class);

    $first = ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Acme', owner_email: 'a@acme.test'));
    $second = ProvisionCompanyAction::run(new ProvisionCompanyData(name: 'Acme', owner_email: 'b@acme.test'));

    expect($first->slug)->toBe('acme')
        ->and($second->slug)->toBe('acme-2');
});

it('reports paid revenue for the current month only', function () {
    $company = Company::factory()->create();

    BillingInvoice::factory()->for($company)->create([
        'status' => 'paid',
        'paid_at' => now(),
        'total_cents' => 12_345,
    ]);
    BillingInvoice::factory()->for($company)->create([
        'status' => 'paid',
        'paid_at' => now()->subMonths(2),
        'period_start' => now()->subMonths(2)->startOfMonth(),
        'period_end' => now()->subMonths(2)->endOfMonth(),
        'total_cents' => 99_900,
    ]);
    BillingInvoice::factory()->for($company)->create([
        'status' => 'open',
        'period_start' => now()->subMonths(3)->startOfMonth(),
        'period_end' => now()->subMonths(3)->endOfMonth(),
        'total_cents' => 50_000,
    ]);

    $this->actingAs(Admin::factory()->create(), 'admin');

    Livewire::test(PlatformStatsWidget::class)
        ->assertSee('Revenue this month')
        ->assertSee('EUR 123.45')   // current-month paid only
        ->assertSee('EUR 500.00'); // outstanding = open invoice
});
