<?php

declare(strict_types=1);

use App\Actions\AcceptInvitationAction;
use App\Actions\ResendInvitationAction;
use App\Actions\RevokeInvitationAction;
use App\Actions\SendInvitationAction;
use App\Data\AcceptInvitationData;
use App\Data\CreateInvitationData;
use App\Events\InvitationAccepted;
use App\Exceptions\InvalidInvitationTokenException;
use App\Mail\InvitationMail;
use App\Models\Company;
use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Services\BuiltInRoles;
use App\Support\Services\CompanyContext;
use Database\Seeders\PermissionSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

function inviteCompany(): Company
{
    test()->seed(PermissionSeeder::class);
    $company = setCompany(Company::factory()->create());
    BuiltInRoles::ensure($company);

    return $company;
}

test('sending an invite creates a pending row and queues the mail on notifications', function () {
    Mail::fake();
    inviteCompany();

    $invitation = SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'employee'));

    expect($invitation->isPending())->toBeTrue()
        ->and($invitation->token)->toBeUuid()
        ->and($invitation->expires_at->diffInDays(now()->addDays(7)))->toBeLessThan(1);

    Mail::assertQueued(InvitationMail::class, fn (InvitationMail $mail): bool => $mail->queue === 'notifications');
});

test('duplicate pending invites, existing members, unknown roles and owner invites are rejected', function () {
    Mail::fake();
    $company = inviteCompany();

    SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'employee'));

    expect(fn () => SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'employee')))
        ->toThrow(ValidationException::class);

    $member = User::factory()->for($company)->create();
    expect(fn () => SendInvitationAction::run(new CreateInvitationData($member->email, 'employee')))
        ->toThrow(ValidationException::class);

    expect(fn () => SendInvitationAction::run(new CreateInvitationData('a@b.nl', 'nonexistent-role')))
        ->toThrow(ValidationException::class);

    expect(fn () => SendInvitationAction::run(new CreateInvitationData('boss@b.nl', 'owner')))
        ->toThrow(ValidationException::class);
});

test('invites of company A are invisible to company B', function () {
    Mail::fake();
    inviteCompany();
    SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'employee'));

    setCompany(Company::factory()->create());

    expect(UserInvitation::query()->count())->toBe(0);
});

test('resending rotates the token so the old link dies', function () {
    Mail::fake();
    inviteCompany();

    $invitation = SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'employee'));
    $oldToken = $invitation->token;

    ResendInvitationAction::run($invitation->id);

    expect($invitation->fresh()->token)->not->toBe($oldToken);

    expect(fn () => AcceptInvitationAction::run(new AcceptInvitationData(
        token: $oldToken, first_name: 'New', last_name: 'Hire', password: 'Sup3r$ecret1234',
    )))->toThrow(InvalidInvitationTokenException::class);
});

test('accepting creates the user in the right company with the invited role and logs in', function () {
    Mail::fake();
    Event::fake([InvitationAccepted::class]);
    $company = inviteCompany();

    $invitation = SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'manager'));

    // Simulate the public request: no tenant context
    app(CompanyContext::class)->forget();

    $user = AcceptInvitationAction::run(new AcceptInvitationData(
        token: $invitation->token, first_name: 'New', last_name: 'Hire', password: 'Sup3r$ecret1234',
    ));

    expect($user->company_id)->toBe($company->id)
        ->and($user->email)->toBe('new@hire.nl')
        ->and($user->email_verified_at)->not->toBeNull()
        ->and($user->hasRole('manager'))->toBeTrue()
        ->and(auth('web')->id())->toBe($user->id)
        ->and($invitation->fresh()->accepted_at)->not->toBeNull();

    Event::assertDispatched(InvitationAccepted::class, fn (InvitationAccepted $event): bool => $event->company_id === $company->id && $event->role === 'manager');
});

test('expired, revoked and already-accepted tokens create no user', function () {
    Mail::fake();
    inviteCompany();

    $expired = SendInvitationAction::run(new CreateInvitationData('late@b.nl', 'employee'));
    $expired->forceFill(['expires_at' => now()->subDay()])->save();

    $revoked = SendInvitationAction::run(new CreateInvitationData('revoked@b.nl', 'employee'));
    RevokeInvitationAction::run($revoked->id);

    $userCount = User::query()->withoutGlobalScopes()->count();

    foreach ([$expired, $revoked] as $invitation) {
        expect(fn () => AcceptInvitationAction::run(new AcceptInvitationData(
            token: $invitation->token, first_name: 'X', last_name: 'Y', password: 'Sup3r$ecret1234',
        )))->toThrow(InvalidInvitationTokenException::class);
    }

    expect(User::query()->withoutGlobalScopes()->count())->toBe($userCount);
});

test('double-accept only ever creates one user', function () {
    Mail::fake();
    inviteCompany();

    $invitation = SendInvitationAction::run(new CreateInvitationData('once@b.nl', 'employee'));

    AcceptInvitationAction::run(new AcceptInvitationData(
        token: $invitation->token, first_name: 'First', last_name: 'Take', password: 'Sup3r$ecret1234',
    ));

    expect(fn () => AcceptInvitationAction::run(new AcceptInvitationData(
        token: $invitation->token, first_name: 'Second', last_name: 'Take', password: 'Sup3r$ecret1234',
    )))->toThrow(InvalidInvitationTokenException::class);

    expect(User::query()->withoutGlobalScopes()->where('email', 'once@b.nl')->count())->toBe(1);
});

test('the public register page renders for a valid token and rejects a dead one', function () {
    Mail::fake();
    inviteCompany();
    $invitation = SendInvitationAction::run(new CreateInvitationData('new@hire.nl', 'employee'));

    $this->get(route('invite.register', ['token' => $invitation->token]))
        ->assertOk()
        ->assertSee('new@hire.nl');

    $this->get(route('invite.register', ['token' => 'dead-token']))
        ->assertOk()
        ->assertSee('no longer valid');
});
