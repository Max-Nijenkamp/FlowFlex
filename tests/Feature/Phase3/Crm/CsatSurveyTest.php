<?php

use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Crm\CsatSurvey;
use App\Models\Crm\Ticket;
use Illuminate\Support\Str;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);

    $this->ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'CSAT test ticket',
        'status'     => TicketStatus::Resolved->value,
        'priority'   => TicketPriority::Normal->value,
    ]);
});

it('can create a csat survey', function () {
    $survey = CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $this->ticket->id,
        'token'      => Str::random(64),
    ]);

    expect($survey)->toBeInstanceOf(CsatSurvey::class);
    expect($survey->ticket_id)->toBe($this->ticket->id);
});

it('csat survey token is unique', function () {
    $token = Str::random(64);

    CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $this->ticket->id,
        'token'      => $token,
    ]);

    $secondTicket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'Second ticket',
        'status'     => TicketStatus::Open->value,
        'priority'   => TicketPriority::Low->value,
    ]);

    expect(fn () => CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $secondTicket->id,
        'token'      => $token,
    ]))->toThrow(\Illuminate\Database\QueryException::class);
});

it('csat survey belongs to ticket', function () {
    $survey = CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $this->ticket->id,
        'token'      => Str::random(64),
    ]);

    expect($survey->ticket->id)->toBe($this->ticket->id);
});

it('ticket has csatSurveys relationship', function () {
    CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $this->ticket->id,
        'token'      => Str::random(64),
    ]);

    expect($this->ticket->csatSurveys()->count())->toBe(1);
});

it('csat survey sent_at and expires_at cast to datetime', function () {
    $survey = CsatSurvey::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'ticket_id'  => $this->ticket->id,
        'token'      => Str::random(64),
        'sent_at'    => '2026-05-07 10:00:00',
        'expires_at' => '2026-05-14 10:00:00',
    ]);

    expect($survey->sent_at)->toBeInstanceOf(\DateTimeInterface::class);
    expect($survey->expires_at)->toBeInstanceOf(\DateTimeInterface::class);
});
