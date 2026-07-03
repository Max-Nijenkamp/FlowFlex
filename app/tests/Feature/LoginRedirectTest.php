<?php

declare(strict_types=1);

use App\Filament\Auth\PanelLogin;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('a stale /admin intended URL cannot hijack an /app login', function () {
    $user = User::factory()->for(Company::factory())->create(['password' => 'secret123']);

    // Simulate having hit /admin logged-out first (stores the intended URL).
    session()->put('url.intended', url('/admin'));

    Filament::setCurrentPanel('app');

    Livewire::test(PanelLogin::class)
        ->fillForm(['email' => $user->email, 'password' => 'secret123'])
        ->call('authenticate')
        ->assertRedirect(url('/app'));
});

test('an /app intended URL is still honored on /app login', function () {
    $user = User::factory()->for(Company::factory())->create(['password' => 'secret123']);

    session()->put('url.intended', url('/app/profile'));

    Filament::setCurrentPanel('app');

    Livewire::test(PanelLogin::class)
        ->fillForm(['email' => $user->email, 'password' => 'secret123'])
        ->call('authenticate')
        ->assertRedirect(url('/app/profile'));
});
