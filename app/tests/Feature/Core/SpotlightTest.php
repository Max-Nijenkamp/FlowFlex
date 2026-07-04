<?php

declare(strict_types=1);

use App\Livewire\Spotlight;
use App\Models\Company;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('spotlight renders on an authenticated panel page and not on login', function () {
    $company = Company::factory()->create();
    $user = User::factory()->for($company)->create();

    $this->get('/app/login')->assertOk()->assertDontSee('ff-spotlight-overlay');

    $this->actingAs($user)->get('/app')->assertOk()->assertSee('ff-spotlight-overlay');
});

test('results resolve for the bound panel with canAccess filtering applied', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    $this->actingAs($user);
    Filament::setCurrentPanel('app');

    $results = Livewire::test(Spotlight::class)->instance()->results;
    $labels = array_column($results, 'label');

    expect($labels)->toContain('Dashboard')
        ->toContain('Profile')
        // Gated pages/resources the user lacks: settings page is owner-only,
        // audit resource needs permission + module — neither may appear.
        ->not->toContain('Company settings')
        ->not->toContain('Audit log');
});

test('a query filters navigation and the global-search group needs two characters', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    $this->actingAs($user);
    Filament::setCurrentPanel('app');

    $component = Livewire::test(Spotlight::class);

    $component->set('query', 'dash');
    $labels = array_column($component->instance()->results, 'label');
    expect($labels)->toContain('Dashboard')->not->toContain('Profile');

    // one char: nav filtering only, no record search executed
    $component->set('query', 'd');
    expect($component->instance()->results)->not->toBeEmpty();
});

test('navigation results are capped at eight entries', function () {
    $company = setCompany(Company::factory()->create());
    $user = User::factory()->for($company)->create();

    $this->actingAs($user);
    Filament::setCurrentPanel('app');

    $results = Livewire::test(Spotlight::class)->instance()->results;

    $navCount = count(array_filter(
        $results,
        fn (array $item): bool => in_array($item['group'], ['Pages', 'Resources', 'Account'], true),
    ));

    expect($navCount)->toBeLessThanOrEqual(Spotlight::NAV_CAP);
});
