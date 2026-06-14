<?php

declare(strict_types=1);

use App\Filament\Admin\Pages\AdminLogin;
use App\Filament\Auth\PanelLogin;
use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Database\Seeders\PermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    app(CompanyContext::class)->forget();

    // Filament login rate-limits 5 attempts/IP — state leaks across tests.
    foreach ([AdminLogin::class, PanelLogin::class] as $component) {
        RateLimiter::clear('livewire-rate-limiter:'.sha1($component.'|authenticate|127.0.0.1'));
    }
});

it('redirects unauthenticated /app requests to login (no context exception)', function () {
    $this->get('/app')->assertRedirect();
});

it('lets a tenant user into /app and sets company context', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $this->actingAs($user, 'web')->get('/app')->assertSuccessful();

    expect(app(CompanyContext::class)->currentId())->toBe($company->id);
});

it('lets a staff admin into /admin', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->get('/admin')->assertSuccessful();
});

it('rejects a tenant user from /admin', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $this->actingAs($user, 'web')->get('/admin')->assertRedirect();
});

it('rejects a staff admin from /app', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->get('/app')->assertRedirect();
});

// Regression: canAccessPanel runs inside Filament Authenticate — BEFORE
// SetCompanyContext sets the team id. Domain panels 403'd for users who DID
// hold access.{panel}-panel because roles loaded under a null team.
it('lets a permitted user switch into domain panels', function (string $path) {
    $this->seed(PermissionSeeder::class);

    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    setPermissionsTeamId($company->id);
    $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web', 'team_id' => $company->id]);
    $owner->syncPermissions(Permission::where('guard_name', 'web')->get());
    $user->assignRole($owner);

    // Simulate a fresh request: no team id set yet when the panel authorizes.
    setPermissionsTeamId(null);
    $user->unsetRelation('roles');

    $this->actingAs($user, 'web')->get($path)->assertSuccessful();
})->with(['/hr', '/finance', '/crm']);

it('serves the login pages', function () {
    $this->get('/app/login')->assertSuccessful();
    $this->get('/admin/login')->assertSuccessful();
});

// Full Livewire submits — would have caught the Admin model missing the
// HasAppAuthentication contract (panel MFA crashes in authenticate()).
it('authenticates a staff admin through the /admin login form', function () {
    $admin = Admin::factory()->create();

    auth('admin')->logout();
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(AdminLogin::class)
        ->set('data.email', $admin->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect();
});

it('authenticates a tenant user through the /app login form', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    auth('web')->logout();
    Filament::setCurrentPanel(Filament::getPanel('app'));

    Livewire::test(PanelLogin::class)
        ->set('data.email', $user->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertHasNoFormErrors()
        ->assertRedirect();
});

// Regression: one shared url.intended bounced logins across guards — a guest
// visit to /admin hijacked the next CUSTOMER login (redirect → /admin →
// /admin/login), and a guest /app visit hijacked the next STAFF login.
// GuardScopedLoginResponse + the PublicAuthController filter scope it per guard.
it('customer login ignores a stale staff intended url', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $this->get('/admin'); // guest → stores url.intended = /admin

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(url('/app'));
});

it('staff login ignores a stale customer intended url', function () {
    $admin = Admin::factory()->create();

    $this->get('/app'); // guest → stores url.intended = /app

    auth('admin')->logout();
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(AdminLogin::class)
        ->set('data.email', $admin->email)
        ->set('data.password', 'password')
        ->call('authenticate')
        ->assertRedirect(url('/admin'));
});

it('customer login still honors a tenant intended url', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create();

    $this->get('/hr'); // guest → stores url.intended = /hr

    $this->post('/login', ['email' => $user->email, 'password' => 'password'])
        ->assertRedirect(url('/hr'));
});
