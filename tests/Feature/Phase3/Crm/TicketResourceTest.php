<?php

use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Crm\Ticket;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    attachModule($this->company, 'crm', 'crm');
    givePermissions($this->tenant, [
        'crm.tickets.view',
        'crm.tickets.create',
        'crm.tickets.edit',
        'crm.tickets.delete',
    ]);

    $this->ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'Cannot export invoices',
        'status'     => TicketStatus::Open->value,
        'priority'   => TicketPriority::Normal->value,
    ]);
});

it('authenticated tenant with permission can list tickets', function () {
    $this->actingAs($this->tenant, 'tenant')
        ->get('/crm/tickets')
        ->assertOk();
});

it('unauthenticated request redirects from tickets list', function () {
    $this->get('/crm/tickets')->assertRedirect();
});

it('tenant without permission gets 403 on tickets list', function () {
    $other = makeTenant($this->company);

    $this->actingAs($other, 'tenant')
        ->get('/crm/tickets')
        ->assertForbidden();
});

it('can create a ticket via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(\App\Filament\Crm\Resources\TicketResource\Pages\CreateTicket::class)
        ->fillForm([
            'subject'  => 'Login broken on mobile',
            'priority' => TicketPriority::High->value,
            'status'   => TicketStatus::Open->value,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Ticket::withoutGlobalScopes()
        ->where('company_id', $this->company->id)
        ->where('subject', 'Login broken on mobile')
        ->exists()
    )->toBeTrue();
});

it('can update a ticket via Filament', function () {
    $this->actingAs($this->tenant, 'tenant');

    \Filament\Facades\Filament::setCurrentPanel('crm');
    Livewire::actingAs($this->tenant, 'tenant')
        ->test(
            \App\Filament\Crm\Resources\TicketResource\Pages\EditTicket::class,
            ['record' => $this->ticket->getRouteKey()]
        )
        ->fillForm(['subject' => 'Cannot export invoices - resolved'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->ticket->fresh()->subject)->toBe('Cannot export invoices - resolved');
});

it('ticket status casts to TicketStatus enum', function () {
    expect($this->ticket->status)->toBe(TicketStatus::Open);
});

it('ticket priority casts to TicketPriority enum', function () {
    expect($this->ticket->priority)->toBe(TicketPriority::Normal);
});

it('tenant from another company cannot see tickets from this company', function () {
    $otherCompany = makeCompany();
    $otherTenant  = makeTenant($otherCompany);
    givePermission($otherTenant, 'crm.tickets.view');

    $this->actingAs($otherTenant, 'tenant');

    expect(Ticket::all()->pluck('id'))->not->toContain($this->ticket->id);
});
