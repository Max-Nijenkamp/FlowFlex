<?php

declare(strict_types=1);

use App\Events\Foundation\UserActivated;
use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Event;

describe('Invite Acceptance', function () {
    beforeEach(function () {
        $this->withoutMiddleware(PreventRequestForgery::class);

        $this->company = Company::factory()->create(['status' => 'active']);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'invited',
            'password'   => null,
        ]);
        $this->token = \Illuminate\Support\Str::random(64);
        $this->invitation = UserInvitation::create([
            'user_id'    => $this->user->id,
            'company_id' => $this->company->id,
            'token'      => $this->token,
            'expires_at' => now()->addDays(7),
        ]);
    });

    it('shows the invite acceptance page for a valid token', function () {
        $response = $this->get("/invite/{$this->token}");
        $response->assertStatus(200);
        $response->assertViewIs('auth.invite');
    });

    it('redirects to expired page for invalid token', function () {
        $response = $this->get('/invite/bad-token-xyz');
        $response->assertRedirect(route('invite.expired'));
    });

    it('redirects to expired page for already-accepted invitation', function () {
        $this->invitation->update(['accepted_at' => now()]);

        $response = $this->get("/invite/{$this->token}");
        $response->assertRedirect(route('invite.expired'));
    });

    it('redirects to expired page for expired invitation', function () {
        $this->invitation->update(['expires_at' => now()->subDay()]);

        $response = $this->get("/invite/{$this->token}");
        $response->assertRedirect(route('invite.expired'));
    });

    it('activates user on valid accept', function () {
        Event::fake([UserActivated::class]);

        $response = $this->post("/invite/{$this->token}", [
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect('/app');

        $this->user->refresh();
        expect($this->user->status)->toBe('active');
        expect($this->user->password)->not->toBeNull();

        $this->invitation->refresh();
        expect($this->invitation->isAccepted())->toBeTrue();

        Event::assertDispatched(UserActivated::class);
    });

    it('sets email_verified_at on invite acceptance', function () {
        Event::fake([UserActivated::class]);

        $this->post("/invite/{$this->token}", [
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->user->refresh();
        expect($this->user->email_verified_at)->not->toBeNull();
    });

    it('requires password confirmation to match', function () {
        $response = $this->post("/invite/{$this->token}", [
            'password'              => 'Password123!',
            'password_confirmation' => 'Different123!',
        ]);

        $response->assertSessionHasErrors('password');
        $this->user->refresh();
        expect($this->user->status)->toBe('invited');
    });

    it('requires minimum password strength', function () {
        $response = $this->post("/invite/{$this->token}", [
            'password'              => 'weak',
            'password_confirmation' => 'weak',
        ]);

        $response->assertSessionHasErrors('password');
    });

    it('shows expired page', function () {
        $response = $this->get(route('invite.expired'));
        $response->assertStatus(200);
        $response->assertViewIs('auth.invite-expired');
    });
});
