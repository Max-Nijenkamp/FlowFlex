<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;

test('both panel login pages render', function () {
    $this->get('/admin/login')->assertOk();
    $this->get('/app/login')->assertOk();
});

test('a tenant user reaches /app but is rejected on /admin', function () {
    $user = User::factory()->for(Company::factory())->create();

    $this->actingAs($user, 'web')->get('/app')->assertOk();
    $this->actingAs($user, 'web')->get('/admin')->assertRedirect(); // sent to admin login, never in
});

test('an admin reaches /admin but is rejected on /app', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')->get('/admin')->assertOk();
    $this->actingAs($admin, 'admin')->get('/app')->assertRedirect();
});

test('SetCompanyContext scopes every authenticated /app request', function () {
    $company = Company::factory()->create();
    $user = User::factory()->for($company)->create();

    $this->actingAs($user, 'web')->get('/app')->assertOk();

    expect(app(CompanyContext::class)->currentId())->toBe($company->id);
});

test('unauthenticated /app redirects to login without a context exception', function () {
    $this->get('/app')->assertRedirect('/app/login');
});

test('a suspended company is blocked from /app with 402', function () {
    $company = Company::factory()->create(['subscription_status' => 'suspended']);
    $user = User::factory()->for($company)->create();

    $this->actingAs($user, 'web')->get('/app')->assertStatus(402);
});
