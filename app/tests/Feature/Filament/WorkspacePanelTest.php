<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;

describe('Workspace Panel', function () {
    it('shows workspace login page', function () {
        $this->get('/app/login')
            ->assertOk();
    });

    it('redirects unauthenticated to login', function () {
        $this->get('/app')
            ->assertRedirectToRoute('filament.app.auth.login');
    });

    it('authenticated user can access workspace dashboard', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web')
            ->get('/app')
            ->assertOk();
    });

    it('authenticated user can access users resource list', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web')
            ->get('/app/users')
            ->assertOk();
    });

    it('authenticated user can access roles resource list', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web')
            ->get('/app/roles')
            ->assertOk();
    });

    it('users list only shows users from same company', function () {
        $companyA = Company::factory()->create();
        $companyB = Company::factory()->create();

        $userA  = User::factory()->forCompany($companyA)->create(['first_name' => 'Alice']);
        $userB  = User::factory()->forCompany($companyB)->create(['first_name' => 'BobOnly']);

        $response = $this->actingAs($userA, 'web')
            ->get('/app/users');

        $response->assertOk();
        $response->assertDontSee('BobOnly');
    });
});
