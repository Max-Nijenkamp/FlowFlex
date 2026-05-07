<?php

use App\Enums\Crm\DealStatus;
use App\Models\Crm\Deal;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.deals.view',
        'crm.deals.create',
        'crm.deals.edit',
        'crm.deals.delete',
    ]);

    $this->deal = Deal::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Enterprise License',
        'value'      => '15000.00',
        'currency'   => 'EUR',
        'status'     => DealStatus::Open->value,
    ]);
});

it('authenticated tenant with permission can list deals', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/deals')
        ->assertOk();
});

it('unauthenticated request redirects from deals list', function () {
    $this->get('/crm/deals')->assertRedirect();
});

it('tenant without permission gets 403 on deals list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/deals')
        ->assertForbidden();
});

it('can create a deal via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\DealResource\Pages\CreateDeal::class)
        ->fillForm([
            'title'  => 'SMB Starter Plan',
            'status' => DealStatus::Open->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Deal::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('title', 'SMB Starter Plan')
        ->exists()
    )->toBeTrue();
});

it('can update a deal via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\DealResource\Pages\EditDeal::class,
            ['record' => $this->deal->getRouteKey()]
        )
        ->fillForm(['title' => 'Enterprise License Updated'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->deal->fresh()->title)->toBe('Enterprise License Updated');
});

it('deal status casts to DealStatus enum', function () {
    expect($this->deal->status)->toBe(DealStatus::Open);
});

it('tenant from another company cannot see deals from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.deals.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Deal::all()->pluck('id'))->not->toContain($this->deal->id);
});
