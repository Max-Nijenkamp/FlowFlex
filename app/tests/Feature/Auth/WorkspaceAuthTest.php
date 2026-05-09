<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use Filament\Auth\Pages\Login;
use Filament\Facades\Filament;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Livewire\Livewire;

describe('Workspace Authentication', function () {
    beforeEach(function () {
        auth()->guard('admin')->logout();
        auth()->guard('web')->logout();
        Filament::setCurrentPanel(Filament::getPanel('app'));
    });

    it('redirects unauthenticated requests to login', function () {
        $this->get('/app')
            ->assertRedirect();
    });

    it('authenticates user with valid credentials', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create([
            'email'    => 'user@test.local',
            'password' => bcrypt('password'),
            'status'   => 'active',
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'user@test.local')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoFormErrors()
            ->assertRedirect();
    });

    it('rejects login with wrong password', function () {
        $company = Company::factory()->create();
        User::factory()->forCompany($company)->create([
            'email'    => 'user@test.local',
            'password' => bcrypt('correct'),
        ]);

        Livewire::test(Login::class)
            ->set('data.email', 'user@test.local')
            ->set('data.password', 'wrong')
            ->call('authenticate')
            ->assertHasFormErrors(['email']);

        $this->assertGuest('web');
    });

    it('logs out user correctly', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web')
            ->withoutMiddleware(VerifyCsrfToken::class)
            ->post('/app/logout')
            ->assertRedirect();

        $this->assertGuest('web');
    });

    it('web session does not bleed into admin guard', function () {
        $company = Company::factory()->create();
        $user    = User::factory()->forCompany($company)->create();

        $this->actingAs($user, 'web');

        $this->assertGuest('admin');
        $this->assertAuthenticatedAs($user, 'web');
    });
});
