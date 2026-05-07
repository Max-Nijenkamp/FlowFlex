<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->user = makeUser([
        'email'    => 'admin@flowflex.test',
        'password' => Hash::make('secret123'),
    ]);
});

it('redirects unauthenticated users away from admin panel', function () {
    $this->get('/admin')->assertRedirect();
});

it('shows the admin login page', function () {
    $this->get('/admin/login')->assertOk();
});

it('web guard authenticates admin user with valid credentials', function () {
    $result = Auth::guard('web')->attempt([
        'email'    => 'admin@flowflex.test',
        'password' => 'secret123',
    ]);

    expect($result)->toBeTrue();
});

it('web guard rejects invalid admin credentials', function () {
    $result = Auth::guard('web')->attempt([
        'email'    => 'admin@flowflex.test',
        'password' => 'wrongpassword',
    ]);

    expect($result)->toBeFalse();
});

it('shows the admin dashboard when authenticated', function () {
    $this->actingAs($this->user, 'web')
        ->get('/admin')
        ->assertOk();
});

it('logs out an authenticated admin user', function () {
    $this->actingAs($this->user, 'web')
        ->post('/admin/logout')
        ->assertRedirect();

    $this->assertGuest('web');
});

it('canAccessPanel returns true for admin panel for User model', function () {
    $panel = \Filament\Facades\Filament::getPanel('admin');

    expect($this->user->canAccessPanel($panel))->toBeTrue();
});

it('canAccessPanel returns false for other panels for User model', function () {
    $panel = \Filament\Facades\Filament::getPanel('workspace');

    expect($this->user->canAccessPanel($panel))->toBeFalse();
});

it('prevents tenant users from accessing the admin panel', function () {
    $company = makeCompany();
    $tenant  = makeTenant($company);

    $this->actingAs($tenant, 'tenant')
        ->get('/admin')
        ->assertRedirect();
});

it('tenant cannot authenticate via web guard', function () {
    $company = makeCompany();
    $tenant  = makeTenant($company, [
        'email'    => 'tenant@flowflex.test',
        'password' => Hash::make('password'),
    ]);

    $result = Auth::guard('web')->attempt([
        'email'    => 'tenant@flowflex.test',
        'password' => 'password',
    ]);

    // Tenant is in a different model/table, so web guard won't find them
    expect($result)->toBeFalse();
});
