<?php

use App\Models\Crm\CannedResponse;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.canned-responses.view',
        'crm.canned-responses.create',
        'crm.canned-responses.edit',
        'crm.canned-responses.delete',
    ]);

    $this->response = CannedResponse::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Thank you for contacting us',
        'body'       => 'We have received your message and will respond within 24 hours.',
        'is_shared'  => true,
    ]);
});

it('authenticated tenant with permission can list canned responses', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/canned-responses')
        ->assertOk();
});

it('unauthenticated request redirects from canned responses list', function () {
    $this->get('/crm/canned-responses')->assertRedirect();
});

it('tenant without permission gets 403 on canned responses list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/canned-responses')
        ->assertForbidden();
});

it('can create a canned response via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\CannedResponseResource\Pages\CreateCannedResponse::class)
        ->fillForm([
            'title' => 'Out of office',
            'body'  => 'We are currently unavailable. Please expect a response within 2 business days.',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(CannedResponse::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('title', 'Out of office')
        ->exists()
    )->toBeTrue();
});

it('can update a canned response via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\CannedResponseResource\Pages\EditCannedResponse::class,
            ['record' => $this->response->getRouteKey()]
        )
        ->fillForm(['title' => 'Thank you for reaching out'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->response->fresh()->title)->toBe('Thank you for reaching out');
});

it('tenant from another company cannot see canned responses from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.canned-responses.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(CannedResponse::all()->pluck('id'))->not->toContain($this->response->id);
});
