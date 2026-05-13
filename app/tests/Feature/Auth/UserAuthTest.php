<?php

use App\Models\User;

it('active user can access app panel', function () {
    $company = createCompany();
    $user = createUser($company, ['status' => 'active']);
    $panel = \Filament\Facades\Filament::getPanel('app');

    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('invited user can access app panel', function () {
    $company = createCompany();
    $user = createUser($company, ['status' => 'invited']);
    $panel = \Filament\Facades\Filament::getPanel('app');

    expect($user->canAccessPanel($panel))->toBeTrue();
});

it('inactive user cannot access app panel', function () {
    $company = createCompany();
    $user = createUser($company, ['status' => 'inactive']);
    $panel = \Filament\Facades\Filament::getPanel('app');

    expect($user->canAccessPanel($panel))->toBeFalse();
});

it('user authenticates via web guard', function () {
    $company = createCompany();
    $user = createUser($company);

    test()->actingAs($user, 'web');

    expect(auth()->guard('web')->check())->toBeTrue();
    expect(auth()->guard('admin')->check())->toBeFalse();
});
