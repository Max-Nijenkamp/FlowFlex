<?php

use App\Models\Admin;
use App\Models\User;

it('admin can authenticate via admin guard', function () {
    $admin = createAdmin();

    test()->actingAs($admin, 'admin');

    expect(auth()->guard('admin')->check())->toBeTrue();
    expect(auth()->guard('web')->check())->toBeFalse();
});

it('web user cannot authenticate via admin guard', function () {
    $company = createCompany();
    $user = createUser($company);

    test()->actingAs($user, 'web');

    expect(auth()->guard('web')->check())->toBeTrue();
    expect(auth()->guard('admin')->check())->toBeFalse();
});

it('admin model can access admin panel', function () {
    $admin = createAdmin();
    $panel = \Filament\Facades\Filament::getPanel('admin');

    expect($admin->canAccessPanel($panel))->toBeTrue();
});

it('admin model cannot access app panel', function () {
    $admin = createAdmin();
    $panel = \Filament\Facades\Filament::getPanel('app');

    expect($admin->canAccessPanel($panel))->toBeFalse();
});
