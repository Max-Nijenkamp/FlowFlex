<?php

declare(strict_types=1);

use App\Models\Admin;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Livewire\Livewire;

describe('Admin Authentication', function () {
    beforeEach(function () {
        auth()->guard('admin')->logout();
        auth()->guard('web')->logout();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    });

    it('shows admin login page', function () {
        $this->get('/admin/login')
            ->assertOk();
    });

    it('redirects unauthenticated admin requests to login', function () {
        $this->get('/admin')
            ->assertRedirectToRoute('filament.admin.auth.login');
    });

    it('authenticates admin with valid credentials', function () {
        $admin = Admin::factory()->create([
            'email'    => 'admin@test.local',
            'password' => bcrypt('password'),
            'role'     => 'super_admin',
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'admin@test.local')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect();
    });

    it('rejects admin login with wrong password', function () {
        Admin::factory()->create([
            'email'    => 'admin@test.local',
            'password' => bcrypt('correct'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'admin@test.local')
            ->set('data.password', 'wrong')
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest('admin');
    });

    it('admin session does not bleed into web guard', function () {
        $admin = Admin::factory()->create(['password' => bcrypt('pass')]);

        $this->actingAs($admin, 'admin');

        $this->assertGuest('web');
        $this->assertAuthenticatedAs($admin, 'admin');
    });

    it('logs out admin correctly', function () {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin')
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/admin/logout')
            ->assertRedirect();

        $this->assertGuest('admin');
    });
});
