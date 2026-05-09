<?php

declare(strict_types=1);

use App\Models\Admin;
use App\Models\Company;
use App\Models\User;

describe('Guard Isolation', function () {
    it('admin guard does not authenticate web requests', function () {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $this->assertGuest('web');
    });

    it('web guard does not authenticate admin requests', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web');

        $this->assertGuest('admin');
    });

    it('admin cannot access app panel routes', function () {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->get('/app')
            ->assertRedirect();
    });

    it('web user cannot access admin panel routes', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web')
            ->get('/admin')
            ->assertRedirectToRoute('filament.admin.auth.login');
    });

    it('authenticated admin can access admin panel', function () {
        $admin = Admin::factory()->superAdmin()->create();

        $this->actingAs($admin, 'admin')
            ->get('/admin')
            ->assertOk();
    });
});
