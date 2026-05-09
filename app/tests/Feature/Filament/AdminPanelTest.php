<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;
use Laravel\Horizon\Horizon;

describe('Admin Panel', function () {
    it('shows admin login page', function () {
        $this->get('/admin/login')
            ->assertOk();
    });

    it('redirects unauthenticated to login', function () {
        $this->get('/admin')
            ->assertRedirectToRoute('filament.admin.auth.login');
    });

    it('authenticated admin can access dashboard', function () {
        $admin = Admin::factory()->superAdmin()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk();
    });

    it('admin dashboard returns 200 not redirect', function () {
        $admin = Admin::factory()->superAdmin()->create();

        $response = $this->actingAs($admin, 'admin')
            ->get('/admin');

        $response->assertOk();
        $response->assertStatus(200);
    });

    it('admin can access companies resource list', function () {
        $admin = Admin::factory()->superAdmin()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin/companies')
            ->assertOk();
    });

    it('horizon auth callback returns true for authenticated admin', function () {
        $admin = Admin::factory()->superAdmin()->create();

        $this->actingAs($admin, 'admin');

        $callback = Horizon::$authUsing;
        $request  = request();

        expect($callback)->not->toBeNull();
        expect($callback($request))->toBeTrue();
    });

    it('horizon auth callback returns false when no admin auth', function () {
        $callback = Horizon::$authUsing;
        $request  = request();

        expect($callback)->not->toBeNull();
        expect($callback($request))->toBeFalse();
    });

    it('horizon returns 403 to unauthenticated', function () {
        $this->get('/horizon')
            ->assertStatus(403);
    });

    it('horizon returns 403 to web-guard user', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web')
            ->get('/horizon')
            ->assertStatus(403);
    });
});
