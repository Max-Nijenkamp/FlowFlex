<?php

use App\Models\Finance\MileageRate;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'finance', 'finance');
    givePermissions($this->tenant, [
        'finance.mileage-rates.view',
        'finance.mileage-rates.create',
        'finance.mileage-rates.edit',
        'finance.mileage-rates.delete',
    ]);

    $this->rate = MileageRate::withoutGlobalScopes()->create([
        'company_id'    => $this->company->id,
        'name'          => 'Standard Car Rate',
        'rate_per_km'   => '0.2300',
        'currency'      => 'EUR',
        'effective_from'=> now()->toDateString(),
    ]);
});

it('authenticated tenant with permission can list mileage rates', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/finance/mileage-rates')
        ->assertOk();
});

it('unauthenticated request redirects from mileage rates list', function () {
    $this->get('/finance/mileage-rates')->assertRedirect();
});

it('tenant without permission gets 403 on mileage rates list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/finance/mileage-rates')
        ->assertForbidden();
});

it('can create a mileage rate via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Finance\Resources\MileageRateResource\Pages\CreateMileageRate::class)
        ->fillForm([
            'name'           => 'Van Rate',
            'rate_per_km'    => '0.3500',
            'currency'       => 'EUR',
            'effective_from' => now()->toDateString(),
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(MileageRate::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Van Rate')
        ->exists()
    )->toBeTrue();
});

it('can update a mileage rate via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('finance');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Finance\Resources\MileageRateResource\Pages\EditMileageRate::class,
            ['record' => $this->rate->getRouteKey()]
        )
        ->fillForm(['name' => 'Standard Car Rate 2026'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->rate->fresh()->name)->toBe('Standard Car Rate 2026');
});

it('tenant from another company cannot see mileage rates from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'finance.mileage-rates.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(MileageRate::all()->pluck('id'))->not->toContain($this->rate->id);
});
