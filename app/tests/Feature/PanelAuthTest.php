<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(fn () => app(CompanyContext::class)->forget());

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
