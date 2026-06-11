<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->company = Company::factory()->create();
    $this->setCompany($this->company);
});

it('blocks unverified users from every panel', function (string $path) {
    $this->seed(PermissionSeeder::class);
    $user = User::factory()->forCompany($this->company)->unverified()->create();
    $user->givePermissionTo('access.hr-panel', 'access.finance-panel', 'access.crm-panel');

    $this->actingAs($user, 'web')
        ->get($path)
        ->assertRedirect(); // → email verification prompt, not the dashboard
})->with(['/app', '/hr', '/finance', '/crm']);

it('lets verified users through', function () {
    $user = User::factory()->forCompany($this->company)->create(); // factory = verified

    $this->actingAs($user, 'web')->get('/app')->assertOk();
});

it('resets verification and re-notifies when email changes', function () {
    Notification::fake();
    $user = User::factory()->forCompany($this->company)->create();
    expect($user->hasVerifiedEmail())->toBeTrue();

    $user->update(['email' => 'new@acme.test']);

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);

    // and the panel is now locked again
    $this->actingAs($user->fresh(), 'web')->get('/app')->assertRedirect();
});

it('does not reset verification on unrelated updates', function () {
    Notification::fake();
    $user = User::factory()->forCompany($this->company)->create();

    $user->update(['first_name' => 'Renamed']);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    Notification::assertNothingSent();
});

it('stores the 2FA secret and recovery codes encrypted, flipping the enabled flag', function () {
    $user = User::factory()->forCompany($this->company)->create();

    $user->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');
    $user->saveAppAuthenticationRecoveryCodes(['code-one', 'code-two']);

    $raw = DB::table('users')->where('id', $user->id)->first();
    expect($raw->app_authentication_secret)->not->toContain('JBSWY3DPEHPK3PXP')
        ->and($raw->app_authentication_recovery_codes)->not->toContain('code-one');

    $fresh = $user->fresh();
    expect($fresh->two_factor_enabled)->toBeTrue()
        ->and($fresh->getAppAuthenticationSecret())->toBe('JBSWY3DPEHPK3PXP')
        ->and($fresh->getAppAuthenticationRecoveryCodes())->toBe(['code-one', 'code-two']);
});

it('disabling 2FA clears the flag', function () {
    $user = User::factory()->forCompany($this->company)->create();
    $user->saveAppAuthenticationSecret('JBSWY3DPEHPK3PXP');

    $user->saveAppAuthenticationSecret(null);

    expect($user->fresh()->two_factor_enabled)->toBeFalse();
});
