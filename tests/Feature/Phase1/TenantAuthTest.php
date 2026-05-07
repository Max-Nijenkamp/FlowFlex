<?php

use App\Models\Company;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company, [
        'email'    => 'tenant@flowflex.test',
        'password' => Hash::make('secret123'),
    ]);
});

it('redirects unauthenticated requests from the workspace panel', function () {
    $this->get('/workspace')->assertRedirect();
});

it('shows the workspace login page', function () {
    $this->get('/workspace/login')->assertOk();
});

it('tenant guard authenticates correctly with valid credentials', function () {
    $result = Auth::guard('tenant')->attempt([
        'email'    => 'tenant@flowflex.test',
        'password' => 'secret123',
    ]);

    expect($result)->toBeTrue();
});

it('tenant guard rejects invalid credentials', function () {
    $result = Auth::guard('tenant')->attempt([
        'email'    => 'tenant@flowflex.test',
        'password' => 'wrongpassword',
    ]);

    expect($result)->toBeFalse();
});

it('logs out an authenticated tenant', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->post('/workspace/logout')
        ->assertRedirect();

    $this->assertGuest('tenant');
});

it('disabled tenant canAccessPanel returns false for workspace', function () {
    $disabled = makeTenant($this->company, [
        'email'      => 'disabled@flowflex.test',
        'password'   => Hash::make('password'),
        'is_enabled' => false,
    ]);

    $panel = \Filament\Facades\Filament::getPanel('workspace');

    // canAccessPanel returns false for disabled tenants
    expect($disabled->canAccessPanel($panel))->toBeFalse();
});

it('canAccessPanel returns false for admin panel for tenant users', function () {
    $panel = \Filament\Facades\Filament::getPanel('admin');

    expect($this->tenant->canAccessPanel($panel))->toBeFalse();
});

it('canAccessPanel returns true for workspace panel for enabled tenant', function () {
    $panel = \Filament\Facades\Filament::getPanel('workspace');

    expect($this->tenant->canAccessPanel($panel))->toBeTrue();
});

it('prevents tenant from accessing the admin panel even when authenticated', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/admin')
        ->assertRedirect();
});

it('user (web guard) cannot be found in tenant guard', function () {
    $admin = makeUser(['email' => 'admin@flowflex.test', 'password' => Hash::make('password')]);

    $result = Auth::guard('tenant')->attempt([
        'email'    => 'admin@flowflex.test',
        'password' => 'password',
    ]);

    expect($result)->toBeFalse();
});
