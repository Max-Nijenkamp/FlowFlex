<?php

declare(strict_types=1);

use App\Filament\App\Pages\CompanySettings;
use App\Models\Company;
use App\Models\User;
use App\Support\Services\CompanyContext;
use Filament\Facades\Filament;
use Livewire\Livewire;

describe('Company Settings', function () {
    beforeEach(function () {
        auth()->guard('web')->logout();
        Filament::setCurrentPanel(Filament::getPanel('app'));

        $this->company = Company::factory()->create([
            'status'   => 'active',
            'slug'     => 'my-company',
            'timezone' => 'UTC',
            'locale'   => 'en',
            'currency' => 'EUR',
        ]);
        $this->user = User::factory()->create([
            'company_id' => $this->company->id,
            'status'     => 'active',
        ]);

        app(CompanyContext::class)->set($this->company);
        $this->actingAs($this->user, 'web');
    });

    it('loads the settings page with current company values', function () {
        Livewire::test(CompanySettings::class)
            ->assertSet('data.name', $this->company->name)
            ->assertSet('data.slug', $this->company->slug)
            ->assertSet('data.timezone', 'UTC');
    });

    it('saves updated company settings', function () {
        Livewire::test(CompanySettings::class)
            ->set('data.name', 'Updated Name')
            ->set('data.slug', 'updated-slug')
            ->set('data.email', 'new@email.com')
            ->set('data.timezone', 'Europe/Amsterdam')
            ->set('data.locale', 'nl')
            ->set('data.currency', 'EUR')
            ->call('save')
            ->assertHasNoErrors();

        $this->company->refresh();
        expect($this->company->name)->toBe('Updated Name');
        expect($this->company->slug)->toBe('updated-slug');
        expect($this->company->timezone)->toBe('Europe/Amsterdam');
    });

    it('rejects a slug that is already taken by another company', function () {
        Company::factory()->create(['slug' => 'taken-slug']);

        Livewire::test(CompanySettings::class)
            ->set('data.name', 'My Company')
            ->set('data.slug', 'taken-slug')
            ->set('data.email', $this->company->email)
            ->set('data.timezone', 'UTC')
            ->set('data.locale', 'en')
            ->set('data.currency', 'EUR')
            ->call('save')
            ->assertHasErrors(['data.slug']);
    });

    it('allows saving with the same slug (own company)', function () {
        Livewire::test(CompanySettings::class)
            ->set('data.name', $this->company->name)
            ->set('data.slug', 'my-company') // same slug, should pass
            ->set('data.email', $this->company->email)
            ->set('data.timezone', 'UTC')
            ->set('data.locale', 'en')
            ->set('data.currency', 'EUR')
            ->call('save')
            ->assertHasNoErrors();
    });
});
