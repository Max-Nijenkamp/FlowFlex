<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

describe('Invite Expiry', function () {
    beforeEach(function () {
        $this->withoutMiddleware(PreventRequestForgery::class);
    });

    it('expired invitation token is rejected with redirect to expired page', function () {
        $company = Company::factory()->create(['status' => 'active']);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'invited',
            'password'   => null,
        ]);

        UserInvitation::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'token'      => 'expired-token-123',
            'expires_at' => now()->subDay(), // expired yesterday
        ]);

        // GET the invite page — should redirect to expired
        $this->get('/invite/expired-token-123')
            ->assertRedirect(route('invite.expired'));
    });

    it('expired invitation cannot be accepted via POST', function () {
        $company = Company::factory()->create(['status' => 'active']);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'invited',
            'password'   => null,
        ]);

        UserInvitation::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'token'      => 'expired-post-token',
            'expires_at' => now()->subDay(),
        ]);

        // POST to accept — should also redirect to expired page
        $this->post('/invite/expired-post-token', [
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect(route('invite.expired'));

        // User should still be in invited status
        $user->refresh();
        expect($user->status)->toBe('invited');
    });

    it('valid invitation token is shown correctly', function () {
        $company = Company::factory()->create(['status' => 'active']);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'invited',
            'password'   => null,
        ]);

        UserInvitation::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'token'      => 'valid-token-456',
            'expires_at' => now()->addDay(), // valid
        ]);

        $this->get('/invite/valid-token-456')
            ->assertStatus(200)
            ->assertViewIs('auth.invite');
    });

    it('valid invitation token can be accepted', function () {
        $company = Company::factory()->create(['status' => 'active']);

        $user = User::factory()->create([
            'company_id' => $company->id,
            'status'     => 'invited',
            'password'   => null,
        ]);

        UserInvitation::create([
            'user_id'    => $user->id,
            'company_id' => $company->id,
            'token'      => 'valid-accept-token',
            'expires_at' => now()->addDay(),
        ]);

        $this->post('/invite/valid-accept-token', [
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect('/app');

        $user->refresh();
        expect($user->status)->toBe('active');
    });
});
