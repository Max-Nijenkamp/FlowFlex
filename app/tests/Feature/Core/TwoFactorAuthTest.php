<?php

declare(strict_types=1);

use App\Models\Company;
use App\Models\User;
use App\Support\Filament\AppAuthenticationWithQrFix;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FA\Google2FA;

test('a double-encoded QR data URI is unwrapped exactly one layer', function () {
    $inner = 'data:image/svg+xml;base64,'.base64_encode('<svg>qr</svg>');
    $doubleWrapped = 'data:image/svg+xml;base64,'.base64_encode($inner);

    expect(AppAuthenticationWithQrFix::unwrapDoubleEncodedDataUri($doubleWrapped))->toBe($inner);
});

test('a correctly single-encoded QR data URI passes through unchanged', function () {
    $valid = 'data:image/svg+xml;base64,'.base64_encode('<svg>qr</svg>');

    expect(AppAuthenticationWithQrFix::unwrapDoubleEncodedDataUri($valid))->toBe($valid)
        ->and(AppAuthenticationWithQrFix::unwrapDoubleEncodedDataUri('not-a-data-uri'))->toBe('not-a-data-uri');
});

test('both panels register the QR-fixed app authentication subclass', function () {
    foreach (['app', 'admin'] as $panelId) {
        $mfa = collect(Filament\Facades\Filament::getPanel($panelId)->getMultiFactorAuthenticationProviders())
            ->first(fn ($provider) => $provider instanceof AppAuthentication);

        expect($mfa)->toBeInstanceOf(AppAuthenticationWithQrFix::class);
    }
});

test('the current TOTP verifies and a wrong or stale code is rejected', function () {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $current = $google2fa->getCurrentOtp($secret);

    expect($google2fa->verifyKey($secret, $current))->not->toBeFalse()
        ->and($google2fa->verifyKey($secret, '000000'))->toBeFalse();
});

test('2FA secret and recovery codes persist encrypted on the user row', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    $user->forceFill([
        'app_authentication_secret' => 'PLAINSECRET123',
        'app_authentication_recovery_codes' => ['code-one', 'code-two'],
    ])->save();

    $raw = DB::table('users')->where('id', $user->id)->first();

    expect($raw->app_authentication_secret)->not->toContain('PLAINSECRET123')
        ->and((string) $raw->app_authentication_recovery_codes)->not->toContain('code-one');

    $fresh = $user->fresh();
    expect($fresh->app_authentication_secret)->toBe('PLAINSECRET123')
        ->and($fresh->app_authentication_recovery_codes)->toBe(['code-one', 'code-two']);
});
