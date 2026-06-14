<?php

declare(strict_types=1);

use App\Livewire\Spotlight;
use App\Models\Admin;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('renders the spotlight on an authenticated panel page', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->get('/admin')
        ->assertSuccessful()
        ->assertSee('ff-spotlight-overlay', escape: false);
});

it('does not render the spotlight on the login page', function () {
    $this->get('/admin/login')
        ->assertSuccessful()
        ->assertDontSee('ff-spotlight-overlay', escape: false);
});

it('finds panel navigation by query', function () {
    $admin = Admin::factory()->create();
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin, 'admin')
        ->test(Spotlight::class, ['panelId' => 'admin'])
        ->set('query', 'compan')
        ->assertSee('Companies');
});

it('offers quick-create actions for matching resources', function () {
    $admin = Admin::factory()->create();
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::actingAs($admin, 'admin')
        ->test(Spotlight::class, ['panelId' => 'admin'])
        ->set('query', 'new comp')
        ->assertSee('Quick actions');
});
