<?php

declare(strict_types=1);

use App\Actions\Core\ResendInvitationAction;
use App\Actions\Core\RevokeInvitationAction;
use App\Actions\Core\SendInvitationAction;
use App\Data\Core\CreateInvitationData;
use App\Mail\Core\InvitationMail;
use App\Models\Company;
use App\Models\Core\UserInvitation;
use App\Models\User;
use App\Support\Scopes\CompanyScope;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Mail::fake();
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
    Role::create(['name' => 'employee', 'guard_name' => 'web']);
    $this->inviter = User::factory()->forCompany($this->company)->create();
    $this->actingAs($this->inviter, 'web');
});

it('sends an invitation and queues the mail on notifications', function () {
    $invitation = SendInvitationAction::run(new CreateInvitationData('new@example.com', 'employee'));

    expect($invitation->company_id)->toBe($this->company->id)
        ->and($invitation->expires_at->isFuture())->toBeTrue();

    Mail::assertQueued(InvitationMail::class, fn (InvitationMail $m) => $m->queue === 'notifications');
});

it('rejects a duplicate pending invitation for the same email', function () {
    SendInvitationAction::run(new CreateInvitationData('dupe@example.com', 'employee'));

    SendInvitationAction::run(new CreateInvitationData('dupe@example.com', 'employee'));
})->throws(ValidationException::class);

it('rejects inviting an existing company user', function () {
    SendInvitationAction::run(new CreateInvitationData($this->inviter->email, 'employee'));
})->throws(ValidationException::class);

it('accepts an invitation: creates a user with the right company + role and logs in', function () {
    $invitation = SendInvitationAction::run(new CreateInvitationData('joiner@example.com', 'employee'));

    auth('web')->logout();

    $this->post("/register/invite/{$invitation->token}", [
        'first_name' => 'New',
        'last_name' => 'Joiner',
        'password' => 'a-very-secure-password-123',
    ])->assertRedirect('/app');

    $user = User::query()->withoutGlobalScope(CompanyScope::class)
        ->where('email', 'joiner@example.com')->firstOrFail();

    expect($user->company_id)->toBe($this->company->id)
        ->and($user->hasRole('employee'))->toBeTrue()
        ->and($invitation->fresh()->accepted_at)->not->toBeNull();
});

it('rejects expired tokens', function () {
    $invitation = UserInvitation::factory()->forCompany($this->company)->expired()->create();

    $this->get("/register/invite/{$invitation->token}")->assertNotFound();
});

it('rejects revoked tokens and a revoke marks the invite unusable', function () {
    $invitation = SendInvitationAction::run(new CreateInvitationData('soon-revoked@example.com', 'employee'));
    RevokeInvitationAction::run($invitation->id);

    $this->get("/register/invite/{$invitation->fresh()->token}")->assertNotFound();
});

it('resend rotates the token, invalidating the old link', function () {
    $invitation = SendInvitationAction::run(new CreateInvitationData('rotate@example.com', 'employee'));
    $oldToken = $invitation->token;

    $fresh = ResendInvitationAction::run($invitation->id);

    expect($fresh->token)->not->toBe($oldToken);
    $this->get("/register/invite/{$oldToken}")->assertNotFound();
    $this->get("/register/invite/{$fresh->token}")->assertOk();
});

it('keeps invitations isolated between companies', function () {
    SendInvitationAction::run(new CreateInvitationData('mine@example.com', 'employee'));

    $other = Company::factory()->create();
    $this->setCompany($other);

    expect(UserInvitation::count())->toBe(0);
});
