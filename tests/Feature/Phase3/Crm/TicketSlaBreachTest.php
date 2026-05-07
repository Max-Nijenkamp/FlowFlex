<?php

use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Crm\Ticket;
use App\Models\Crm\TicketSlaBreach;
use App\Models\Crm\TicketSlaRule;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'SLA breach test ticket',
        'status'     => TicketStatus::Open->value,
        'priority'   => TicketPriority::High->value,
    ]);
});

it('can create a ticket SLA breach record', function () {
    $breach = TicketSlaBreach::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'ticket_id'   => $this->ticket->id,
        'type'        => 'first_response',
        'breached_at' => now(),
    ]);

    expect($breach)->toBeInstanceOf(TicketSlaBreach::class);
    expect($breach->type)->toBe('first_response');
});

it('SLA breach is scoped to company', function () {
    $breach = TicketSlaBreach::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'ticket_id'   => $this->ticket->id,
        'type'        => 'resolution',
        'breached_at' => now(),
    ]);

    $this->actingAs($this->tenant, 'tenant');

    $found = TicketSlaBreach::find($breach->id);
    expect($found)->not->toBeNull();
    expect($found->company_id)->toBe($this->company->id);
});

it('SLA breach belongs to ticket', function () {
    $breach = TicketSlaBreach::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'ticket_id'   => $this->ticket->id,
        'type'        => 'first_response',
        'breached_at' => now(),
    ]);

    expect($breach->ticket->id)->toBe($this->ticket->id);
});

it('ticket has slaBreaches relationship', function () {
    TicketSlaBreach::withoutGlobalScopes()->create([
        'company_id'  => $this->company->id,
        'ticket_id'   => $this->ticket->id,
        'type'        => 'resolution',
        'breached_at' => now(),
    ]);

    expect($this->ticket->slaBreaches()->count())->toBe(1);
});

it('SLA breach can optionally reference a sla rule', function () {
    $rule = TicketSlaRule::withoutGlobalScopes()->create([
        'company_id'           => $this->company->id,
        'name'                 => 'High SLA',
        'priority'             => TicketPriority::High->value,
        'first_response_hours' => 2,
        'resolution_hours'     => 8,
        'is_active'            => true,
    ]);

    $breach = TicketSlaBreach::withoutGlobalScopes()->create([
        'company_id'         => $this->company->id,
        'ticket_id'          => $this->ticket->id,
        'ticket_sla_rule_id' => $rule->id,
        'type'               => 'first_response',
        'breached_at'        => now(),
    ]);

    expect($breach->ticketSlaRule->id)->toBe($rule->id);
});

it('SLA breach does not use soft deletes', function () {
    expect(in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive(TicketSlaBreach::class)))->toBeFalse();
});
