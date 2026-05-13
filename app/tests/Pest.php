<?php

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Illuminate\Support\Facades\RateLimiter;

uses(
    Tests\TestCase::class,
    Illuminate\Foundation\Testing\RefreshDatabase::class,
)->in('Feature');

uses(Tests\TestCase::class)->in('Unit');

// Helpers

function createCompany(array $attributes = []): Company
{
    return Company::factory()->create($attributes);
}

function createUser(Company $company, array $attributes = []): User
{
    return User::factory()->create(['company_id' => $company->id, ...$attributes]);
}

function createAdmin(array $attributes = []): Admin
{
    return Admin::factory()->create($attributes);
}

function actingAsOwner(User $user): void
{
    test()->actingAs($user, 'web');
    app(CompanyContext::class)->set($user->company);
}

function actingAsAdmin(Admin $admin): void
{
    test()->actingAs($admin, 'admin');
}

// Reset state between tests
beforeEach(function () {
    auth()->guard('admin')->logout();
    auth()->guard('web')->logout();
    app(CompanyContext::class)->clear();

    // Clear Filament rate limiter to prevent test failures from sequential login attempts
    try {
        RateLimiter::clear('livewire-rate-limiter:' . sha1(\Filament\Auth\Pages\Login::class . '|authenticate|127.0.0.1'));
    } catch (\Throwable) {
        // Rate limiter class may differ by Filament version — silently ignore
    }
});
