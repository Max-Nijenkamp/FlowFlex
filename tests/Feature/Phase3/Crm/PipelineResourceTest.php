<?php

use App\Models\Crm\Pipeline;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.pipelines.view',
        'crm.pipelines.create',
        'crm.pipelines.edit',
        'crm.pipelines.delete',
    ]);

    $this->pipeline = Pipeline::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'name'       => 'Sales Pipeline',
    ]);
});

it('authenticated tenant with permission can list pipelines', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/pipelines')
        ->assertOk();
});

it('unauthenticated request redirects from pipelines list', function () {
    $this->get('/crm/pipelines')->assertRedirect();
});

it('tenant without permission gets 403 on pipelines list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/pipelines')
        ->assertForbidden();
});

it('can create a pipeline via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\PipelineResource\Pages\CreatePipeline::class)
        ->fillForm(['name' => 'Partner Pipeline'])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Pipeline::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Partner Pipeline')
        ->exists()
    )->toBeTrue();
});

it('can update a pipeline via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\PipelineResource\Pages\EditPipeline::class,
            ['record' => $this->pipeline->getRouteKey()]
        )
        ->fillForm(['name' => 'Enterprise Sales Pipeline'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->pipeline->fresh()->name)->toBe('Enterprise Sales Pipeline');
});

it('tenant from another company cannot see pipelines from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.pipelines.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Pipeline::all()->pluck('id'))->not->toContain($this->pipeline->id);
});
