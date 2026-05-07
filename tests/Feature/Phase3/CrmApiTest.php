<?php

use App\Enums\Crm\ContactType;
use App\Enums\Crm\DealStatus;
use App\Enums\Crm\TicketPriority;
use App\Enums\Crm\TicketStatus;
use App\Models\Crm\CrmContact;
use App\Models\Crm\Deal;
use App\Models\Crm\Ticket;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->company = makeCompany();
    $this->tenant  = makeTenant($this->company);
    ['key' => $this->plainKey] = makeApiKey($this->company);

    $this->contact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'first_name' => 'Alice',
        'last_name'  => 'Wonder',
        'email'      => 'alice@example.com',
        'type'       => ContactType::Customer->value,
    ]);

    $this->deal = Deal::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'title'      => 'Big Enterprise Deal',
        'value'      => '50000.00',
        'currency'   => 'EUR',
        'status'     => DealStatus::Open->value,
    ]);

    $this->ticket = Ticket::withoutGlobalScopes()->create([
        'company_id' => $this->company->id,
        'subject'    => 'Cannot log in',
        'status'     => TicketStatus::Open->value,
        'priority'   => TicketPriority::High->value,
    ]);
});

// ---------- Contacts ----------

it('GET /api/v1/crm/contacts returns 401 without API key', function () {
    $this->getJson('/api/v1/crm/contacts')
        ->assertUnauthorized();
});

it('GET /api/v1/crm/contacts returns 200 with valid API key', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/crm/contacts')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page', 'last_page'],
        ]);
});

it('GET /api/v1/crm/contacts/{id} returns single contact', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson("/api/v1/crm/contacts/{$this->contact->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $this->contact->id)
        ->assertJsonPath('data.full_name', 'Alice Wonder');
});

it('crm contacts API is scoped to the authenticated company', function () {
    $otherCompany = makeCompany();
    ['key' => $otherKey] = makeApiKey($otherCompany);

    $otherContact = CrmContact::withoutGlobalScopes()->create([
        'company_id' => $otherCompany->id,
        'first_name' => 'Bob',
        'last_name'  => 'Other',
        'type'       => ContactType::Lead->value,
    ]);

    $response = $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/crm/contacts')
        ->assertOk();

    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($this->contact->id)
                ->not->toContain($otherContact->id);
});

// ---------- Deals ----------

it('GET /api/v1/crm/deals returns 401 without API key', function () {
    $this->getJson('/api/v1/crm/deals')
        ->assertUnauthorized();
});

it('GET /api/v1/crm/deals returns 200 with valid API key', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/crm/deals')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page', 'last_page'],
        ]);
});

it('GET /api/v1/crm/deals/{id} returns single deal', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson("/api/v1/crm/deals/{$this->deal->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $this->deal->id)
        ->assertJsonPath('data.title', 'Big Enterprise Deal');
});

// ---------- Tickets ----------

it('GET /api/v1/crm/tickets returns 401 without API key', function () {
    $this->getJson('/api/v1/crm/tickets')
        ->assertUnauthorized();
});

it('GET /api/v1/crm/tickets returns 200 with valid API key', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson('/api/v1/crm/tickets')
        ->assertOk()
        ->assertJsonStructure([
            'data',
            'meta' => ['total', 'per_page', 'current_page', 'last_page'],
        ]);
});

it('GET /api/v1/crm/tickets/{id} returns single ticket', function () {
    $this->withHeaders(['X-API-Key' => $this->plainKey])
        ->getJson("/api/v1/crm/tickets/{$this->ticket->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $this->ticket->id)
        ->assertJsonPath('data.subject', 'Cannot log in');
});
