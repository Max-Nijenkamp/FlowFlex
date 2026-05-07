<?php

use App\Enums\Crm\TicketPriority;
use App\Models\Crm\TicketSlaRule;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.ticket-sla-rules.view',
        'crm.ticket-sla-rules.create',
        'crm.ticket-sla-rules.edit',
        'crm.ticket-sla-rules.delete',
    ]);

    $this->rule = TicketSlaRule::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'High Priority SLA',
        'priority'             => TicketPriority::High->value,
        'first_response_hours' => 2,
        'resolution_hours'     => 8,
        'is_active'            => true,
    ]);
});

it('authenticated tenant with permission can list SLA rules', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/ticket-sla-rules')
        ->assertOk();
});

it('unauthenticated request redirects from SLA rules list', function () {
    $this->get('/crm/ticket-sla-rules')->assertRedirect();
});

it('tenant without permission gets 403 on SLA rules list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/ticket-sla-rules')
        ->assertForbidden();
});

it('can create a ticket SLA rule via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\TicketSlaRuleResource\Pages\CreateTicketSlaRule::class)
        ->fillForm([
            'name'                 => 'Urgent SLA',
            'priority'             => TicketPriority::Urgent->value,
            'first_response_hours' => 1,
            'resolution_hours'     => 4,
            'is_active'            => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(TicketSlaRule::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('name', 'Urgent SLA')
        ->exists()
    )->toBeTrue();
});

it('can update a ticket SLA rule via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\TicketSlaRuleResource\Pages\EditTicketSlaRule::class,
            ['record' => $this->rule->getRouteKey()]
        )
        ->fillForm(['resolution_hours' => 12])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->rule->fresh()->resolution_hours)->toBe(12);
});

it('SLA rule priority casts to TicketPriority enum', function () {
    expect($this->rule->priority)->toBe(TicketPriority::High);
});

it('tenant from another company cannot see SLA rules from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.ticket-sla-rules.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(TicketSlaRule::all()->pluck('id'))->not->toContain($this->rule->id);
});
