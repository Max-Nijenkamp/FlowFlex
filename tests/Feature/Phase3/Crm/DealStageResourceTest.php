<?php

use App\Models\Crm\DealStage;
use App\Models\Crm\Pipeline;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.deal-stages.view',
        'crm.deal-stages.create',
        'crm.deal-stages.edit',
        'crm.deal-stages.delete',
    ]);

    $this->pipeline = Pipeline::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Main Pipeline',
    ]);

    $this->stage = DealStage::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'pipeline_id' => $this->pipeline->id,
        'name'        => 'Prospecting',
        'sort_order'  => 1,
    ]);
});

it('authenticated tenant with permission can list deal stages', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/deal-stages')
        ->assertOk();
});

it('unauthenticated request redirects from deal stages list', function () {
    $this->get('/crm/deal-stages')->assertRedirect();
});

it('tenant without permission gets 403 on deal stages list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/deal-stages')
        ->assertForbidden();
});

it('can create a deal stage via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\DealStageResource\Pages\CreateDealStage::class)
        ->fillForm([
            'pipeline_id' => $this->pipeline->id,
            'name'        => 'Qualification',
            'sort_order'  => 2,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(DealStage::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Qualification')
        ->exists()
    )->toBeTrue();
});

it('can update a deal stage via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\DealStageResource\Pages\EditDealStage::class,
            ['record' => $this->stage->getRouteKey()]
        )
        ->fillForm(['name' => 'Lead'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->stage->fresh()->name)->toBe('Lead');
});

it('tenant from another company cannot see deal stages from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.deal-stages.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(DealStage::all()->pluck('id'))->not->toContain($this->stage->id);
});
