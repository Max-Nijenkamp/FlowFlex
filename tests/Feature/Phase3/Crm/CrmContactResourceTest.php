<?php

use App\Enums\Crm\ContactType;
use App\Models\Crm\CrmContact;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.contacts.view',
        'crm.contacts.create',
        'crm.contacts.edit',
        'crm.contacts.delete',
    ]);

    $this->contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Alice',
        'last_name'  => 'Wonderland',
        'type'       => ContactType::Lead->value,
    ]);
});

it('authenticated tenant with permission can list crm contacts', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/crm-contacts')
        ->assertOk();
});

it('unauthenticated request redirects from crm contacts list', function () {
    $this->get('/crm/crm-contacts')->assertRedirect();
});

it('tenant without permission gets 403 on crm contacts list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/crm-contacts')
        ->assertForbidden();
});

it('can create a crm contact via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\CrmContactResource\Pages\CreateCrmContact::class)
        ->fillForm([
            'first_name' => 'Bob',
            'last_name'  => 'Builder',
            'type'       => ContactType::Customer->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(CrmContact::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('first_name', 'Bob')
        ->where('last_name', 'Builder')
        ->exists()
    )->toBeTrue();
});

it('can update a crm contact via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\CrmContactResource\Pages\EditCrmContact::class,
            ['record' => $this->contact->getRouteKey()]
        )
        ->fillForm(['first_name' => 'Alicia'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->contact->fresh()->first_name)->toBe('Alicia');
});

it('contact type casts to ContactType enum', function () {
    expect($this->contact->type)->toBe(ContactType::Lead);
});

it('full_name accessor returns concatenated name', function () {
    expect($this->contact->full_name)->toBe('Alice Wonderland');
});

it('tenant from another company cannot see contacts from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.contacts.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(CrmContact::all()->pluck('id'))->not->toContain($this->contact->id);
});
