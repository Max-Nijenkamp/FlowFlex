<?php

declare(strict_types=1);

use App\Filament\Admin\Pages\AdminLogin;
use App\Filament\Auth\PanelLogin;
use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

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
