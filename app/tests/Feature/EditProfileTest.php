<?php

declare(strict_types=1);

use App\Filament\Auth\EditProfile;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(fn () => app(CompanyContext::class)->forget());

it('saves first and last name from the profile page', function () {
    $company = Company::factory()->create();
    $user = User::factory()->forCompany($company)->create([
        'first_name' => 'Old',
        'last_name' => 'Name',
    ]);

    Filament::setCurrentPanel(Filament::getPanel('app'));

    Livewire::actingAs($user, 'web')
        ->test(EditProfile::class)
        ->fillForm([
            'first_name' => 'Marieke',
            'last_name' => 'Jansen',
            'email' => $user->email,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();
    expect($user->first_name)->toBe('Marieke')
        ->and($user->last_name)->toBe('Jansen');
});
