<?php

use App\Models\Crm\CrmCompany;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.companies.view',
        'crm.companies.create',
        'crm.companies.edit',
        'crm.companies.delete',
    ]);

    $this->crmCompany = CrmCompany::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Acme Corp',
    ]);
});

it('authenticated tenant with permission can list crm companies', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/crm-companies')
        ->assertOk();
});

it('unauthenticated request redirects from crm companies list', function () {
    $this->get('/crm/crm-companies')->assertRedirect();
});

it('tenant without permission gets 403 on crm companies list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/crm-companies')
        ->assertForbidden();
});

it('can create a crm company via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\CrmCompanyResource\Pages\CreateCrmCompany::class)
        ->fillForm(['name' => 'Globex Inc'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(CrmCompany::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Globex Inc')
        ->exists()
    )->toBeTrue();
});

it('can update a crm company via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\CrmCompanyResource\Pages\EditCrmCompany::class,
            ['record' => $this->crmCompany->getRouteKey()]
        )
        ->fillForm(['name' => 'Acme Corporation'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->crmCompany->fresh()->name)->toBe('Acme Corporation');
});

it('tenant from another company cannot see crm companies from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.companies.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(CrmCompany::all()->pluck('id'))->not->toContain($this->crmCompany->id);
});
